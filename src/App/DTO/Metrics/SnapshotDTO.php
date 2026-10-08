<?php

declare(strict_types=1);

/**
 * Description of SnapshotDTO.php
 *
 * One stored snapshot as it is (GET snapshots/{grain}/{period}): money in cents, durations in seconds, counts.
 * The payload is the raw measure map; read it with the metric registry, or prefer POST metrics/query.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\PeriodDTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class SnapshotDTO extends DTO
{
    protected ScopeDTO $scope;

    protected PeriodDTO $period;

    protected string $currency;

    protected int $ordersCount;

    protected int $computedAt;

    protected string $measuresVersion;

    /** @var array<string, mixed> */
    protected array $payload = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'scope' => ScopeDTO::fromResponse(ResponseField::array($data, 'scope')),
            'period' => PeriodDTO::fromResponse(ResponseField::array($data, 'period')),
            'currency' => ResponseField::string($data, 'currency'),
            'ordersCount' => ResponseField::int($data, 'orders_count'),
            'computedAt' => ResponseField::int($data, 'computed_at'),
            'measuresVersion' => ResponseField::string($data, 'measures_version'),
            'payload' => ResponseField::object($data, 'payload'),
        ]);
    }

    public function getScope(): ScopeDTO
    {
        return $this->scope;
    }

    public function getPeriod(): PeriodDTO
    {
        return $this->period;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }

    /**
     * Unix timestamp (UTC seconds).
     */
    public function getComputedAt(): int
    {
        return $this->computedAt;
    }

    public function getMeasuresVersion(): string
    {
        return $this->measuresVersion;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
