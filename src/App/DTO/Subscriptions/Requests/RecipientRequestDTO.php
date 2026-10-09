<?php

declare(strict_types=1);

/**
 * Description of RecipientRequestDTO.php
 *
 * One recipient in a subscription create or update body. A recipient has exactly one address, matching the
 * subscription channel: an email (email channel), or a Telegram chat id or a livesite user token (telegram channel).
 * Build it through email(), telegramChat() or telegramUser(); only the set address is sent.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions\Requests;

use Dots\Data\DTO;

class RecipientRequestDTO extends DTO
{
    protected ?string $email = null;

    protected ?int $telegramChatId = null;

    /**
     * The livesite user token (uuid, at most 64 characters) — notifier-bot identifies users by token; it resolves the user's connected Telegram chat.
     */
    protected ?string $telegramUserId = null;

    protected ?string $name = null;

    /**
     * An email recipient; kept for callers written before the telegram channel.
     */
    public static function create(string $email, ?string $name = null): static
    {
        return static::email($email, $name);
    }

    public static function email(string $email, ?string $name = null): static
    {
        return static::fromArray(['email' => $email, 'name' => $name]);
    }

    /**
     * A Telegram chat already connected to the account's notifier bot.
     */
    public static function telegramChat(int $telegramChatId, ?string $name = null): static
    {
        return static::fromArray(['telegramChatId' => $telegramChatId, 'name' => $name]);
    }

    /**
     * A livesite user of the account who connected Telegram to the notifier bot.
     */
    public static function telegramUser(string $telegramUserId, ?string $name = null): static
    {
        return static::fromArray(['telegramUserId' => $telegramUserId, 'name' => $name]);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelegramChatId(): ?int
    {
        return $this->telegramChatId;
    }

    public function getTelegramUserId(): ?string
    {
        return $this->telegramUserId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * The set address and the name, e.g. {"telegram_user_id": "7", "name": "Owner"}.
     *
     * @return array<string, string|int|null>
     */
    public function toRequestBody(): array
    {
        $body = [];
        if ($this->email !== null) {
            $body['email'] = $this->email;
        }

        if ($this->telegramChatId !== null) {
            $body['telegram_chat_id'] = $this->telegramChatId;
        }

        if ($this->telegramUserId !== null) {
            $body['telegram_user_id'] = $this->telegramUserId;
        }

        $body['name'] = $this->name;

        return $body;
    }
}
