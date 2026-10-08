<?php

declare(strict_types=1);

/**
 * Description of MetricCatalogueEntryDTO.php
 *
 * One metric registry entry of GET metrics, labelled in the asked language, with whether it is available for
 * the account.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class MetricCatalogueEntryDTO extends DTO
{
    protected string $key;

    protected string $label;

    protected string $definition;

    protected string $unit;

    protected string $kind;

    protected string $goodDirection;

    protected bool $isMergeable;

    protected int $version;

    protected bool $isAvailable;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'key' => ResponseField::string($data, 'key'),
            'label' => ResponseField::string($data, 'label'),
            'definition' => ResponseField::string($data, 'definition'),
            'unit' => ResponseField::string($data, 'unit'),
            'kind' => ResponseField::string($data, 'kind'),
            'goodDirection' => ResponseField::string($data, 'good_direction'),
            'isMergeable' => ResponseField::bool($data, 'mergeable'),
            'version' => ResponseField::int($data, 'version'),
            'isAvailable' => ResponseField::bool($data, 'available'),
        ]);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * up, down or neutral: which change is good (colours the deltas).
     */
    public function getGoodDirection(): string
    {
        return $this->goodDirection;
    }

    public function isMergeable(): bool
    {
        return $this->isMergeable;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }
}
