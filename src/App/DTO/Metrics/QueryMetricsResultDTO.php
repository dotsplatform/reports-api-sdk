<?php

declare(strict_types=1);

/**
 * Description of QueryMetricsResultDTO.php
 *
 * The response of POST metrics/query: the account currency, the scope and one entry per period.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class QueryMetricsResultDTO extends DTO
{
    protected string $accountId;

    protected string $currency;

    protected ScopeDTO $scope;

    /** @var list<PeriodMetricsDTO> */
    protected array $periods = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        $periods = [];
        foreach (ResponseField::listOfObjects($data, 'periods') as $periodData) {
            $periods[] = PeriodMetricsDTO::fromResponse($periodData);
        }

        return static::fromArray([
            'accountId' => ResponseField::string($data, 'account_id'),
            'currency' => ResponseField::string($data, 'currency'),
            'scope' => ScopeDTO::fromResponse(ResponseField::array($data, 'scope')),
            'periods' => $periods,
        ]);
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getScope(): ScopeDTO
    {
        return $this->scope;
    }

    /**
     * @return list<PeriodMetricsDTO>
     */
    public function getPeriods(): array
    {
        return $this->periods;
    }
}
