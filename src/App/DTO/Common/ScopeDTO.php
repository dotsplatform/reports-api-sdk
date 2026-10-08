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
    protected string $scopeTypeValue;

    protected ?string $scopeId = null;

    public static function create(ScopeType $scopeType, ?string $scopeId): static
    {
        return static::fromArray([
            'scopeTypeValue' => $scopeType->value,
            'scopeId' => $scopeId,
        ]);
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'scopeTypeValue' => ResponseField::string($data, 'type'),
            'scopeId' => ResponseField::nullableString($data, 'id'),
        ]);
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getScopeTypeValue(): string
    {
        return $this->scopeTypeValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getScopeTypeValue()).
     */
    public function getScopeType(): ?ScopeType
    {
        return ScopeType::tryFrom($this->scopeTypeValue);
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
            'type' => $this->scopeTypeValue,
            'id' => $this->scopeId,
        ];
    }
}
