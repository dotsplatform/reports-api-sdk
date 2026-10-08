<?php

declare(strict_types=1);

/**
 * Description of PeriodComparisonDTO.php
 *
 * The comparison period of one queried period: its dates, its metric values and the deltas.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class PeriodComparisonDTO extends DTO
{
    protected string $fromDate;

    protected string $toDate;

    /** @var array<string, MetricValueDTO> */
    protected array $metrics = [];

    /** @var array<string, MetricDeltaDTO> */
    protected array $deltas = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'fromDate' => ResponseField::string($data, 'from'),
            'toDate' => ResponseField::string($data, 'to'),
            'metrics' => MetricValueDTO::mapFromResponse(ResponseField::objectOfObjects($data, 'metrics')),
            'deltas' => MetricDeltaDTO::mapFromResponse(ResponseField::objectOfObjects($data, 'deltas')),
        ]);
    }

    public function getFromDate(): string
    {
        return $this->fromDate;
    }

    public function getToDate(): string
    {
        return $this->toDate;
    }

    /**
     * @return array<string, MetricValueDTO>
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    public function getMetric(string $metricKey): ?MetricValueDTO
    {
        return $this->metrics[$metricKey] ?? null;
    }

    /**
     * @return array<string, MetricDeltaDTO>
     */
    public function getDeltas(): array
    {
        return $this->deltas;
    }

    public function getDelta(string $metricKey): ?MetricDeltaDTO
    {
        return $this->deltas[$metricKey] ?? null;
    }
}
