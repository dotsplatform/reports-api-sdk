<?php

declare(strict_types=1);

/**
 * Description of MetricDeltaDTO.php
 *
 * The change of a metric against the comparison period: absolute in the metric unit (money also as MoneyDTO)
 * and relative (Δ / |previous|, null when the previous value is 0).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\MoneyDTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class MetricDeltaDTO extends DTO
{
    protected int|float $absolute;

    protected ?MoneyDTO $absoluteMoney = null;

    protected int|float|null $relative = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        $absoluteMoney = self::readAbsoluteMoney($data);

        return static::fromArray([
            'absolute' => self::readAbsolute($data, $absoluteMoney),
            'absoluteMoney' => $absoluteMoney,
            'relative' => ResponseField::nullableNumber($data, 'relative'),
        ]);
    }

    /**
     * @param  array<string, array<array-key, mixed>>  $deltasData
     * @return array<string, MetricDeltaDTO>
     */
    public static function mapFromResponse(array $deltasData): array
    {
        $deltas = [];
        foreach ($deltasData as $metricKey => $deltaData) {
            $deltas[$metricKey] = self::fromResponse($deltaData);
        }

        return $deltas;
    }

    /**
     * In the metric unit; cents for money.
     */
    public function getAbsolute(): int|float
    {
        return $this->absolute;
    }

    public function getAbsoluteMoney(): ?MoneyDTO
    {
        return $this->absoluteMoney;
    }

    public function getRelative(): int|float|null
    {
        return $this->relative;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function readAbsoluteMoney(array $data): ?MoneyDTO
    {
        $absoluteData = $data['absolute'] ?? null;
        if (! is_array($absoluteData)) {
            return null;
        }

        return MoneyDTO::fromResponse($absoluteData);
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function readAbsolute(array $data, ?MoneyDTO $absoluteMoney): int|float
    {
        if ($absoluteMoney !== null) {
            return $absoluteMoney->getAmountCents();
        }

        return ResponseField::number($data, 'absolute');
    }
}
