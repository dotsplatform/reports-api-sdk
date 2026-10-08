<?php

declare(strict_types=1);

/**
 * Description of SubscriptionsClient.php
 *
 * subscriptions CRUD, send-now and the delivery log. Reads need reports:read, writes subscriptions:write (account
 * tokens); internal callers may do both. Write calls take the optional X-Actor-Id.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Contracts;

use Dotsplatform\ReportsApi\DTO\Subscriptions\DeliveryDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\Requests\SubscriptionFormDTO;
use Dotsplatform\ReportsApi\DTO\Subscriptions\SubscriptionDTO;

interface SubscriptionsClient
{
    /**
     * @return list<SubscriptionDTO>
     */
    public function list(string $accountId): array;

    public function create(string $accountId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO;

    public function get(string $accountId, string $subscriptionId): SubscriptionDTO;

    /**
     * Only the set fields change; recipients, when set, replace the whole list.
     */
    public function update(string $accountId, string $subscriptionId, SubscriptionFormDTO $form, ?string $actorId = null): SubscriptionDTO;

    public function delete(string $accountId, string $subscriptionId, ?string $actorId = null): void;

    /**
     * Queues a send of the last closed period right away.
     *
     * @return string the queue status ("queued")
     */
    public function sendNow(string $accountId, string $subscriptionId, ?string $actorId = null): string;

    /**
     * The latest 100 deliveries, newest first (not paged).
     *
     * @return list<DeliveryDTO>
     */
    public function listDeliveries(string $accountId, string $subscriptionId): array;
}
