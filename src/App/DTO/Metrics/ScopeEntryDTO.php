<?php

declare(strict_types=1);

/**
 * Description of ScopeEntryDTO.php
 *
 * One scope of GET scopes: the account, a city, a company or an address, with its uuid and name.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ScopeEntryDTO extends DTO
{
    protected string $scopeTypeValue;

    protected string $scopeId;

    protected string $name;

    protected ?string $parentId = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'scopeTypeValue' => ResponseField::string($data, 'type'),
            'scopeId' => ResponseField::string($data, 'id'),
            'name' => ResponseField::string($data, 'name'),
            'parentId' => ResponseField::nullableString($data, 'parent_id'),
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

    public function getScopeId(): string
    {
        return $this->scopeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * The scope as the query endpoints take it.
     */
    public function toScope(): ScopeDTO
    {
        return ScopeDTO::fromArray([
            'scopeTypeValue' => $this->scopeTypeValue,
            'scopeId' => $this->scopeId,
        ]);
    }
}
