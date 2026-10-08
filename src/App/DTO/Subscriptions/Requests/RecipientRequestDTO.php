<?php

declare(strict_types=1);

/**
 * Description of RecipientRequestDTO.php
 *
 * One recipient in a subscription create or update body.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions\Requests;

use Dots\Data\DTO;

class RecipientRequestDTO extends DTO
{
    protected string $email;

    protected ?string $name = null;

    public static function create(string $email, ?string $name = null): static
    {
        return static::fromArray(['email' => $email, 'name' => $name]);
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
     * @return array{email: string, name: string|null}
     */
    public function toRequestBody(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
