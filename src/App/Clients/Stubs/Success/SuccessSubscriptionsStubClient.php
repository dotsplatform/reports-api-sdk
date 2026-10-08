<?php

declare(strict_types=1);

/**
 * Description of SuccessSubscriptionsStubClient.php
 *
 * Answers every subscriptions call with StubResponses data, no HTTP.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Success;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Contracts\SubscriptionsClient;
use Dotsplatform\ReportsApi\DTO\Subscriptions\DeliveryDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\SubscriptionFormDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\SubscriptionDTO;

class SuccessSubscriptionsStubClient implements SubscriptionsClient
{
    private const string QUEUED_STATUS = 'queued';

    public function list(string $accountId): array
    {
        return [SubscriptionDTO::fromResponse(StubResponses::subscription())];
    }

    public function create(string $accountId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO
    {
        return SubscriptionDTO::fromResponse(StubResponses::subscription());
    }

    public function get(string $accountId, string $subscriptionId): SubscriptionDTO
    {
        return SubscriptionDTO::fromResponse(StubResponses::subscription($subscriptionId));
    }

    public function update(string $accountId, string $subscriptionId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO
    {
        return SubscriptionDTO::fromResponse(StubResponses::subscription($subscriptionId));
    }

    public function delete(string $accountId, string $subscriptionId, ?string $actorId = null): void
    {
    }

    public function sendNow(string $accountId, string $subscriptionId, ?string $actorId = null): string
    {
        return self::QUEUED_STATUS;
    }

    public function listDeliveries(string $accountId, string $subscriptionId): array
    {
        return [DeliveryDTO::fromResponse(StubResponses::delivery())];
    }
}
