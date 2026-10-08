<?php

declare(strict_types=1);

/**
 * Description of FailMetricsStubClient.php
 *
 * Every metrics call throws ReportsServerException (503).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Contracts\MetricsClient;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\QueryMetricsResultDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\Requests\QueryMetricsRequestDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\ScopesPageDTO;
use Dotsplatform\ReportsApi\DTO\Metrics\SnapshotDTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportLocale;

class FailMetricsStubClient implements MetricsClient
{
    public function listMetrics(string $accountId, ?ReportLocale $locale = null): array
    {
        throw StubFailure::make();
    }

    public function listDimensions(string $accountId, ?ReportLocale $locale = null): array
    {
        throw StubFailure::make();
    }

    public function listScopes(string $accountId, ?string $cursor = null, ?int $limit = null): ScopesPageDTO
    {
        throw StubFailure::make();
    }

    public function queryMetrics(string $accountId, QueryMetricsRequestDTO $request): QueryMetricsResultDTO
    {
        throw StubFailure::make();
    }

    public function getSnapshot(string $accountId, Grain $grain, string $periodKey, ?ScopeDTO $scope = null): SnapshotDTO
    {
        throw StubFailure::make();
    }
}
