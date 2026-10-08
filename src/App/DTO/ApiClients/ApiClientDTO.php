<?php

declare(strict_types=1);

/**
 * Description of ApiClientDTO.php
 *
 * An account API client (task §11.1): a named token with abilities, owned by one account. The token itself is
 * never returned after creation.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\ApiClients;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ApiClientDTO extends DTO
{
    protected string $id;

    protected string $name;

    /** @var list<string> */
    protected array $abilities = [];

    protected ?string $createdBy = null;

    protected int $createdAt;

    protected ?int $lastUsedAt = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'name' => ResponseField::string($data, 'name'),
            'abilities' => ResponseField::stringList($data, 'abilities'),
            'createdBy' => ResponseField::nullableString($data, 'created_by'),
            'createdAt' => ResponseField::int($data, 'created_at'),
            'lastUsedAt' => ResponseField::nullableInt($data, 'last_used_at'),
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Ability strings ("metrics:read", …); compare with ApiAbility values.
     *
     * @return list<string>
     */
    public function getAbilities(): array
    {
        return $this->abilities;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    /**
     * Unix timestamp.
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Unix timestamp; null when the token was never used.
     */
    public function getLastUsedAt(): ?int
    {
        return $this->lastUsedAt;
    }
}
