<?php

declare(strict_types=1);

/**
 * Description of PeriodDTO.php
 *
 * A period in a response: {"grain": "month", "key": "2026-09"}. Some responses add the local dates it covers
 * ("from" / "to") or a display label. The grain is a string because query results also use "custom".
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Common;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class PeriodDTO extends DTO
{
    protected string $grain;

    protected string $key;

    protected ?string $fromDate = null;

    protected ?string $toDate = null;

    protected ?string $label = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'grain' => ResponseField::string($data, 'grain'),
            'key' => ResponseField::string($data, 'key'),
            'fromDate' => ResponseField::nullableString($data, 'from'),
            'toDate' => ResponseField::nullableString($data, 'to'),
            'label' => ResponseField::nullableString($data, 'label'),
        ]);
    }

    public function getGrain(): string
    {
        return $this->grain;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * The first local date of the period, YYYY-MM-DD.
     */
    public function getFromDate(): ?string
    {
        return $this->fromDate;
    }

    /**
     * The last local date of the period, YYYY-MM-DD (inclusive).
     */
    public function getToDate(): ?string
    {
        return $this->toDate;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
