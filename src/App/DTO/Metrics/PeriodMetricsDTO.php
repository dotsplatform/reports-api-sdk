<?php

declare(strict_types=1);

/**
 * Description of PeriodMetricsDTO.php
 *
 * One period of a metrics query: the period, when its data was computed, the metric values and the comparison.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\PeriodDTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class PeriodMetricsDTO extends DTO
{
    protected PeriodDTO $period;

    protected ?string $dataAsOf = null;

    /** @var array<string, MetricValueDTO> */
    protected array $metrics = [];

    protected ?PeriodComparisonDTO $comparison = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        $comparison = null;
        $comparisonData = ResponseField::nullableArray($data, 'compare');
        if ($comparisonData !== null) {
            $comparison = PeriodComparisonDTO::fromResponse($comparisonData);
        }

        return static::fromArray([
            'period' => PeriodDTO::fromResponse($data),
            'dataAsOf' => ResponseField::nullableString($data, 'data_as_of'),
            'metrics' => MetricValueDTO::mapFromResponse(ResponseField::objectOfObjects($data, 'metrics')),
            'comparison' => $comparison,
        ]);
    }

    /**
     * Grain, key and the local dates the period covers.
     */
    public function getPeriod(): PeriodDTO
    {
        return $this->period;
    }

    /**
     * ISO 8601 UTC time of the newest data in this period; null when nothing was computed yet.
     */
    public function getDataAsOf(): ?string
    {
        return $this->dataAsOf;
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

    public function getComparison(): ?PeriodComparisonDTO
    {
        return $this->comparison;
    }
}
