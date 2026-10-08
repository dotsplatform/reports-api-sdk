<?php

declare(strict_types=1);

/**
 * Description of CreateApiClientRequestDTO.php
 *
 * The body of POST api-clients. Without abilities the service grants read-only (metrics:read, reports:read).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\ApiClients\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\ApiAbility;

class CreateApiClientRequestDTO extends DTO
{
    protected string $name;

    /** @var list<ApiAbility> */
    protected array $abilities = [];

    /**
     * @param  list<ApiAbility>  $abilities
     */
    public static function create(string $name, array $abilities = []): static
    {
        return static::fromArray(['name' => $name, 'abilities' => $abilities]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return list<ApiAbility>
     */
    public function getAbilities(): array
    {
        return $this->abilities;
    }

    /**
     * @return array{name: string, abilities?: list<string>}
     */
    public function toRequestBody(): array
    {
        $body = ['name' => $this->name];
        if ($this->abilities === []) {
            return $body;
        }

        $abilityValues = [];
        foreach ($this->abilities as $ability) {
            $abilityValues[] = $ability->value;
        }

        $body['abilities'] = $abilityValues;

        return $body;
    }
}
