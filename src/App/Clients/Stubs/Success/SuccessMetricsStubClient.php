<?php

declare(strict_types=1);

/**
 * Description of SuccessMetricsStubClient.php
 *
 * Answers every metrics call with StubResponses data, no HTTP.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Success;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Contracts\MetricsClient;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\DimensionDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\MetricCatalogueEntryDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\QueryMetricsResultDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\QueryMetricsRequestDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\ScopesPageDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\SnapshotDTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportLocale;

class SuccessMetricsStubClient implements MetricsClient
{
    public function listMetrics(string $accountId, ?ReportLocale $locale = null): array
    {
        return [MetricCatalogueEntryDTO::fromResponse(StubResponses::metricCatalogueEntry())];
    }

    public function listDimensions(string $accountId, ?ReportLocale $locale = null): array
    {
        return [DimensionDTO::fromResponse(StubResponses::dimension())];
    }

    public function listScopes(string $accountId, ?string $cursor = null, ?int $limit = null): ScopesPageDTO
    {
        return ScopesPageDTO::fromResponse(StubResponses::scopesPage());
    }

    public function queryMetrics(string $accountId, QueryMetricsRequestDTO $request): QueryMetricsResultDTO
    {
        return QueryMetricsResultDTO::fromResponse(StubResponses::queryMetricsResult());
    }

    public function getSnapshot(string $accountId, Grain $grain, string $periodKey, ?ScopeDTO $scope = null): SnapshotDTO
    {
        return SnapshotDTO::fromResponse(StubResponses::snapshot());
    }
}
