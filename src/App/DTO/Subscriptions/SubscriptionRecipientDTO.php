<?php

declare(strict_types=1);

/**
 * Description of SubscriptionRecipientDTO.php
 *
 * One recipient of a subscription and whether reports still go to it. An email recipient has the email; a Telegram
 * recipient has the chat id and/or the livesite user id it was added by.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\RecipientKind;
use Dotsplatform\ReportsApi\Enums\RecipientStatus;
use Dotsplatform\ReportsApi\Support\ResponseField;

class SubscriptionRecipientDTO extends DTO
{
    /**
     * The value used when the service is older than the recipient "kind" field: every recipient was an email then.
     */
    private const string LEGACY_KIND_VALUE = 'email';

    protected string $id;

    protected string $kindValue;

    protected ?string $email = null;

    protected ?int $telegramChatId = null;

    protected ?string $telegramUserId = null;

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
            'kindValue' => self::readKindValue($data),
            'email' => ResponseField::nullableString($data, 'email'),
            'telegramChatId' => ResponseField::nullableInt($data, 'telegram_chat_id'),
            'telegramUserId' => ResponseField::nullableString($data, 'telegram_user_id'),
            'name' => ResponseField::nullableString($data, 'name'),
            'statusValue' => ResponseField::string($data, 'status'),
            'unsubscribedAt' => ResponseField::nullableInt($data, 'unsubscribed_at'),
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getKindValue(): string
    {
        return $this->kindValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getKindValue()).
     */
    public function getKind(): ?RecipientKind
    {
        return RecipientKind::tryFrom($this->kindValue);
    }

    public function isTelegram(): bool
    {
        return $this->getKind() === RecipientKind::TELEGRAM;
    }

    /**
     * Null for a Telegram recipient.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelegramChatId(): ?int
    {
        return $this->telegramChatId;
    }

    /**
     * The livesite user id the Telegram recipient was added by; null for a chat added by its id.
     */
    public function getTelegramUserId(): ?string
    {
        return $this->telegramUserId;
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

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function readKindValue(array $data): string
    {
        $kindValue = ResponseField::nullableString($data, 'kind');
        if ($kindValue === null) {
            return self::LEGACY_KIND_VALUE;
        }

        return $kindValue;
    }
}
