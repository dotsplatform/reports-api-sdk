<?php

declare(strict_types=1);

/**
 * Description of SubscriptionsHttpClient.php
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Http;

use Dotsplatform\ReportsApi\Contracts\SubscriptionsClient;
use Dotsplatform\ReportsApi\DTO\Subscriptions\DeliveryDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\SubscriptionFormDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\SubscriptionDTO;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Support\ResponseField;

class SubscriptionsHttpClient implements SubscriptionsClient
{
    private const string SUBSCRIPTIONS_URL_TEMPLATE = 'api/v1/accounts/%s/subscriptions';

    private const string SUBSCRIPTION_URL_TEMPLATE = 'api/v1/accounts/%s/subscriptions/%s';

    private const string SEND_NOW_URL_TEMPLATE = 'api/v1/accounts/%s/subscriptions/%s/send-now';

    private const string DELIVERIES_URL_TEMPLATE = 'api/v1/accounts/%s/subscriptions/%s/deliveries';

    public function __construct(
        private readonly ReportsHttpTransport $transport,
    ) {
    }

    public function list(string $accountId): array
    {
        $path = $this->transport->buildPath(self::SUBSCRIPTIONS_URL_TEMPLATE, $accountId);
        $response = $this->transport->getJson($path);

        $subscriptions = [];
        foreach (ResponseField::listOfObjects($response, 'data') as $subscriptionData) {
            $subscriptions[] = SubscriptionDTO::fromResponse($subscriptionData);
        }

        return $subscriptions;
    }

    public function create(string $accountId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO
    {
        $path = $this->transport->buildPath(self::SUBSCRIPTIONS_URL_TEMPLATE, $accountId);
        $response = $this->transport->postJson($path, $form->toRequestBody(), $actorId);

        return SubscriptionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function get(string $accountId, string $subscriptionId): SubscriptionDTO
    {
        $path = $this->transport->buildPath(self::SUBSCRIPTION_URL_TEMPLATE, $accountId, $subscriptionId);
        $response = $this->transport->getJson($path);

        return SubscriptionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function update(string $accountId, string $subscriptionId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO
    {
        $path = $this->transport->buildPath(self::SUBSCRIPTION_URL_TEMPLATE, $accountId, $subscriptionId);
        $response = $this->transport->patchJson($path, $form->toRequestBody(), $actorId);

        return SubscriptionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function delete(string $accountId, string $subscriptionId, ?string $actorId = null): void
    {
        $path = $this->transport->buildPath(self::SUBSCRIPTION_URL_TEMPLATE, $accountId, $subscriptionId);
        $this->transport->delete($path, $actorId);
    }

    public function sendNow(string $accountId, string $subscriptionId, ?string $actorId = null): string
    {
        $path = $this->transport->buildPath(self::SEND_NOW_URL_TEMPLATE, $accountId, $subscriptionId);
        $response = $this->transport->postJson($path, [], $actorId);

        return ResponseField::string(ResponseField::array($response, 'data'), 'status');
    }

    public function listDeliveries(string $accountId, string $subscriptionId): array
    {
        $path = $this->transport->buildPath(self::DELIVERIES_URL_TEMPLATE, $accountId, $subscriptionId);
        $response = $this->transport->getJson($path);

        $deliveries = [];
        foreach (ResponseField::listOfObjects($response, 'data') as $deliveryData) {
            $deliveries[] = DeliveryDTO::fromResponse($deliveryData);
        }

        return $deliveries;
    }
}
