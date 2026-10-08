<?php

declare(strict_types=1);

/**
 * Description of MetricsClient.php
 *
 * GET metrics, GET dimensions, GET scopes, POST metrics/query, GET snapshots/{grain}/{period}.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Contracts;

use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\DimensionDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\MetricCatalogueEntryDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\QueryMetricsResultDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\QueryMetricsRequestDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\ScopesPageDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\SnapshotDTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportLocale;

interface MetricsClient
{
    /**
     * The metric registry, labelled in the locale (English by default), with availability for the account.
     *
     * @return list<MetricCatalogueEntryDTO>
     */
    public function listMetrics(string $accountId, ?ReportLocale $locale = null): array;

    /**
     * @return list<DimensionDTO>
     */
    public function listDimensions(string $accountId, ?ReportLocale $locale = null): array;

    /**
     * The account, its cities, companies and addresses. Pass the previous page next cursor to read on.
     */
    public function listScopes(string $accountId, ?string $cursor = null, ?int $limit = null): ScopesPageDTO;

    public function queryMetrics(string $accountId, QueryMetricsRequestDTO $request): QueryMetricsResultDTO;

    /**
     * One stored snapshot; without a scope, the account snapshot. Throws ReportsNotFoundException when the period
     * was never aggregated.
     */
    public function getSnapshot(string $accountId, Grain $grain, string $periodKey, ?ScopeDTO $scope = null): SnapshotDTO;
}
