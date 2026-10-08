<?php

declare(strict_types=1);

/**
 * Description of MetricValueDTO.php
 *
 * One metric value of POST metrics/query (task §11.2). The response shape depends on the metric:
 * - a single value: money {amount_cents, currency}, a duration {seconds}, a distance {meters} or {value};
 *   ratios and means add {numerator, denominator};
 * - a breakdown: {values: {<dimension value>: <single value>}};
 * - a top list: {entries: [{id, label, values}]}.
 * getNumber() always returns the single value in the metric unit (cents for money).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\MoneyDTO;
use Dotsplatform\ReportsApi\DTO\Common\RatioDTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class MetricValueDTO extends DTO
{
    public const string UNIT_MONEY = 'money';

    public const string UNIT_SECONDS = 'seconds';

    public const string UNIT_METERS = 'meters';

    /**
     * The JSON keys a single value can come in, by unit.
     */
    private const array SCALAR_KEYS = ['amount_cents', 'seconds', 'meters', 'value'];

    protected string $kind;

    protected ?string $unit = null;

    protected int|float|null $number = null;

    protected ?MoneyDTO $money = null;

    protected ?RatioDTO $ratio = null;

    /** @var array<string, MetricValueDTO>|null */
    protected ?array $breakdown = null;

    /** @var list<TopEntryDTO>|null */
    protected ?array $topEntries = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        $kind = ResponseField::string($data, 'kind');
        $unit = ResponseField::nullableString($data, 'unit');

        if (array_key_exists('entries', $data)) {
            return static::fromArray([
                'kind' => $kind,
                'unit' => $unit,
                'topEntries' => self::readTopEntries($data),
            ]);
        }

        if (array_key_exists('values', $data)) {
            return static::fromArray([
                'kind' => $kind,
                'unit' => $unit,
                'breakdown' => self::readBreakdown($data, $kind, $unit),
            ]);
        }

        return static::fromSingleValueResponse($data, $kind, $unit);
    }

    /**
     * A single value; breakdown values arrive without their own kind and unit.
     *
     * @param  array<array-key, mixed>  $data
     */
    public static function fromSingleValueResponse(array $data, string $kind, ?string $unit): static
    {
        $number = self::readSingleNumber($data);

        return static::fromArray([
            'kind' => $kind,
            'unit' => $unit,
            'number' => $number,
            'money' => self::readMoney($data),
            'ratio' => self::readRatio($data, $number),
        ]);
    }

    /**
     * @param  array<string, array<array-key, mixed>>  $metricsData
     * @return array<string, MetricValueDTO>
     */
    public static function mapFromResponse(array $metricsData): array
    {
        $metrics = [];
        foreach ($metricsData as $metricKey => $metricData) {
            $metrics[$metricKey] = self::fromResponse($metricData);
        }

        return $metrics;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * count, money, seconds, meters, rating, share. Null for top lists.
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * The single value in the metric unit: cents for money, seconds, meters, or the plain number.
     */
    public function getNumber(): int|float|null
    {
        return $this->number;
    }

    public function getMoney(): ?MoneyDTO
    {
        return $this->money;
    }

    public function getRatio(): ?RatioDTO
    {
        return $this->ratio;
    }

    /**
     * @return array<string, MetricValueDTO>|null
     */
    public function getBreakdown(): ?array
    {
        return $this->breakdown;
    }

    /**
     * @return list<TopEntryDTO>|null
     */
    public function getTopEntries(): ?array
    {
        return $this->topEntries;
    }

    public function isMoney(): bool
    {
        return $this->unit === self::UNIT_MONEY;
    }

    public function isBreakdown(): bool
    {
        return $this->breakdown !== null;
    }

    public function isTopList(): bool
    {
        return $this->topEntries !== null;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function readSingleNumber(array $data): int|float|null
    {
        foreach (self::SCALAR_KEYS as $scalarKey) {
            if (array_key_exists($scalarKey, $data)) {
                return ResponseField::nullableNumber($data, $scalarKey);
            }
        }

        return null;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function readMoney(array $data): ?MoneyDTO
    {
        $amountCents = ResponseField::nullableInt($data, 'amount_cents');
        if ($amountCents === null) {
            return null;
        }

        return MoneyDTO::fromResponse($data);
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function readRatio(array $data, int|float|null $number): ?RatioDTO
    {
        if (! array_key_exists('denominator', $data)) {
            return null;
        }

        return RatioDTO::fromArray([
            'numerator' => ResponseField::nullableInt($data, 'numerator'),
            'denominator' => ResponseField::int($data, 'denominator'),
            'value' => $number,
        ]);
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<string, MetricValueDTO>
     */
    private static function readBreakdown(array $data, string $kind, ?string $unit): array
    {
        $breakdown = [];
        foreach (ResponseField::objectOfObjects($data, 'values') as $dimensionValue => $valueData) {
            $breakdown[$dimensionValue] = self::fromSingleValueResponse($valueData, $kind, $unit);
        }

        return $breakdown;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return list<TopEntryDTO>
     */
    private static function readTopEntries(array $data): array
    {
        $entries = [];
        foreach (ResponseField::listOfObjects($data, 'entries') as $entryData) {
            $entries[] = TopEntryDTO::fromResponse($entryData);
        }

        return $entries;
    }
}
