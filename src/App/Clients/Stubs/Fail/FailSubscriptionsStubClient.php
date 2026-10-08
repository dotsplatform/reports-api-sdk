<?php

declare(strict_types=1);

/**
 * Description of FailSubscriptionsStubClient.php
 *
 * Every subscriptions call throws ReportsServerException (503).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Contracts\SubscriptionsClient;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\SubscriptionFormDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\SubscriptionDTO;

class FailSubscriptionsStubClient implements SubscriptionsClient
{
    public function list(string $accountId): array
    {
        throw StubFailure::make();
    }

    public function create(string $accountId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO
    {
        throw StubFailure::make();
    }

    public function get(string $accountId, string $subscriptionId): SubscriptionDTO
    {
        throw StubFailure::make();
    }

    public function update(string $accountId, string $subscriptionId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO
    {
        throw StubFailure::make();
    }

    public function delete(string $accountId, string $subscriptionId, ?string $actorId = null): void
    {
        throw StubFailure::make();
    }

    public function sendNow(string $accountId, string $subscriptionId, ?string $actorId = null): string
    {
        throw StubFailure::make();
    }

    public function listDeliveries(string $accountId, string $subscriptionId): array
    {
        throw StubFailure::make();
    }
}
