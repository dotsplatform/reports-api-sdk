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

    protected RecipientStatus $status;

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
            'status' => ResponseField::enum($data, 'status', RecipientStatus::class),
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

    public function getStatus(): RecipientStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === RecipientStatus::ACTIVE;
    }

    /**
     * Unix timestamp; null while subscribed.
     */
    public function getUnsubscribedAt(): ?int
    {
        return $this->unsubscribedAt;
    }
}
