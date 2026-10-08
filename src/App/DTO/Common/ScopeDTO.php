<?php

declare(strict_types=1);

/**
 * Description of ScopeDTO.php
 *
 * The level a value belongs to: {"type": "city", "id": "<uuid>"}. The id is null only on report definitions that
 * follow the account (scope type "account" without a fixed id).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Common;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ScopeDTO extends DTO
{
    protected ScopeType $scopeType;

    protected ?string $scopeId = null;

    public static function create(ScopeType $scopeType, ?string $scopeId): static
    {
        return static::fromArray([
            'scopeType' => $scopeType,
            'scopeId' => $scopeId,
        ]);
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'scopeType' => ResponseField::enum($data, 'type', ScopeType::class),
            'scopeId' => ResponseField::nullableString($data, 'id'),
        ]);
    }

    public function getScopeType(): ScopeType
    {
        return $this->scopeType;
    }

    public function getScopeId(): ?string
    {
        return $this->scopeId;
    }

    /**
     * @return array{type: string, id: string|null}
     */
    public function toRequestBody(): array
    {
        return [
            'type' => $this->scopeType->value,
            'id' => $this->scopeId,
        ];
    }
}
