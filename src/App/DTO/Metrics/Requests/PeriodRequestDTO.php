<?php

declare(strict_types=1);

/**
 * Description of PeriodRequestDTO.php
 *
 * One period of POST metrics/query: a preset, plus a key for MONTH ("2026-09") and YEAR ("2025"), or local
 * from / to dates (YYYY-MM-DD, up to one year) for CUSTOM.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\PeriodPreset;

class PeriodRequestDTO extends DTO
{
    protected PeriodPreset $preset;

    protected ?string $key = null;

    protected ?string $fromDate = null;

    protected ?string $toDate = null;

    public static function preset(PeriodPreset $preset): static
    {
        return static::fromArray(['preset' => $preset]);
    }

    public static function month(string $monthKey): static
    {
        return static::fromArray(['preset' => PeriodPreset::MONTH, 'key' => $monthKey]);
    }

    public static function year(string $yearKey): static
    {
        return static::fromArray(['preset' => PeriodPreset::YEAR, 'key' => $yearKey]);
    }

    public static function custom(string $fromDate, string $toDate): static
    {
        return static::fromArray(['preset' => PeriodPreset::CUSTOM, 'fromDate' => $fromDate, 'toDate' => $toDate]);
    }

    public function getPreset(): PeriodPreset
    {
        return $this->preset;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getFromDate(): ?string
    {
        return $this->fromDate;
    }

    public function getToDate(): ?string
    {
        return $this->toDate;
    }

    /**
     * @return array<string, string>
     */
    public function toRequestBody(): array
    {
        $body = ['preset' => $this->preset->value];
        if ($this->key !== null) {
            $body['key'] = $this->key;
        }

        if ($this->fromDate !== null) {
            $body['from'] = $this->fromDate;
        }

        if ($this->toDate !== null) {
            $body['to'] = $this->toDate;
        }

        return $body;
    }
}
