<?php

declare(strict_types=1);

/**
 * Description of DeliveryDTO.php
 *
 * One delivery of a subscription to one recipient (GET subscriptions/{id}/deliveries).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\DeliveryStatus;
use Dotsplatform\ReportsApi\Enums\SubscriptionChannel;
use Dotsplatform\ReportsApi\Support\ResponseField;

class DeliveryDTO extends DTO
{
    protected string $id;

    protected string $recipientId;

    protected ?string $recipientEmail = null;

    protected string $sendKey;

    protected string $runId;

    protected SubscriptionChannel $channel;

    protected DeliveryStatus $status;

    protected ?string $error = null;

    protected ?int $sentAt = null;

    protected ?int $deliveredAt = null;

    protected int $createdAt;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'recipientId' => ResponseField::string($data, 'recipient_id'),
            'recipientEmail' => ResponseField::nullableString($data, 'recipient_email'),
            'sendKey' => ResponseField::string($data, 'send_key'),
            'runId' => ResponseField::string($data, 'run_id'),
            'channel' => ResponseField::enum($data, 'channel', SubscriptionChannel::class),
            'status' => ResponseField::enum($data, 'status', DeliveryStatus::class),
            'error' => ResponseField::nullableString($data, 'error'),
            'sentAt' => ResponseField::nullableInt($data, 'sent_at'),
            'deliveredAt' => ResponseField::nullableInt($data, 'delivered_at'),
            'createdAt' => ResponseField::int($data, 'created_at'),
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRecipientId(): string
    {
        return $this->recipientId;
    }

    /**
     * Null when the recipient was removed since.
     */
    public function getRecipientEmail(): ?string
    {
        return $this->recipientEmail;
    }

    /**
     * The idempotency key of one scheduled send (subscription + period).
     */
    public function getSendKey(): string
    {
        return $this->sendKey;
    }

    public function getRunId(): string
    {
        return $this->runId;
    }

    public function getChannel(): SubscriptionChannel
    {
        return $this->channel;
    }

    public function getStatus(): DeliveryStatus
    {
        return $this->status;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Unix timestamp.
     */
    public function getSentAt(): ?int
    {
        return $this->sentAt;
    }

    /**
     * Unix timestamp.
     */
    public function getDeliveredAt(): ?int
    {
        return $this->deliveredAt;
    }

    /**
     * Unix timestamp.
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }
}
