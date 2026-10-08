<?php

declare(strict_types=1);

/**
 * Description of QueryMetricsRequestDTO.php
 *
 * The body of POST metrics/query (task §11.2): metric keys, a scope, and exactly one way to pick periods:
 * one period, a list of periods, or a series. Optionally a comparison.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;

class QueryMetricsRequestDTO extends DTO
{
    /** @var list<string> */
    protected array $metricKeys = [];

    protected ScopeDTO $scope;

    protected ?PeriodRequestDTO $period = null;

    /** @var list<PeriodRequestDTO> */
    protected array $periods = [];

    protected ?SeriesRequestDTO $series = null;

    protected ?CompareMode $compare = null;

    /**
     * @return list<string>
     */
    public function getMetricKeys(): array
    {
        return $this->metricKeys;
    }

    public function getScope(): ScopeDTO
    {
        return $this->scope;
    }

    public function getPeriod(): ?PeriodRequestDTO
    {
        return $this->period;
    }

    /**
     * @return list<PeriodRequestDTO>
     */
    public function getPeriods(): array
    {
        return $this->periods;
    }

    public function getSeries(): ?SeriesRequestDTO
    {
        return $this->series;
    }

    public function getCompare(): ?CompareMode
    {
        return $this->compare;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestBody(): array
    {
        $body = [
            'metrics' => $this->metricKeys,
            'scope' => $this->scope->toRequestBody(),
        ];

        if ($this->period !== null) {
            $body['period'] = $this->period->toRequestBody();
        }

        if ($this->periods !== []) {
            $body['periods'] = $this->buildPeriodsBody();
        }

        if ($this->series !== null) {
            $body['series'] = $this->series->toRequestBody();
        }

        if ($this->compare !== null) {
            $body['compare'] = $this->compare->value;
        }

        return $body;
    }

    /**
     * @return list<array<string, string>>
     */
    private function buildPeriodsBody(): array
    {
        $periodsBody = [];
        foreach ($this->periods as $period) {
            $periodsBody[] = $period->toRequestBody();
        }

        return $periodsBody;
    }
}
