<?php

declare(strict_types=1);

/**
 * Description of SubscriptionRecipientDTO.php
 *
 * One recipient of a subscription and whether mail still goes to it.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\RecipientStatus;
use Dotsplatform\ReportsApi\Support\ResponseField;

class SubscriptionRecipientDTO extends DTO
{
    protected string $id;

    protected string $email;

    protected ?string $name = null;

    protected string $statusValue;

    protected ?int $unsubscribedAt = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'email' => ResponseField::string($data, 'email'),
            'name' => ResponseField::nullableString($data, 'name'),
            'statusValue' => ResponseField::string($data, 'status'),
            'unsubscribedAt' => ResponseField::nullableInt($data, 'unsubscribed_at'),
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getStatusValue(): string
    {
        return $this->statusValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getStatusValue()).
     */
    public function getStatus(): ?RecipientStatus
    {
        return RecipientStatus::tryFrom($this->statusValue);
    }

    public function isActive(): bool
    {
        return $this->getStatus() === RecipientStatus::ACTIVE;
    }

    /**
     * Unix timestamp; null while subscribed.
     */
    public function getUnsubscribedAt(): ?int
    {
        return $this->unsubscribedAt;
    }
}
