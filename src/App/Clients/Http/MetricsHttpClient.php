<?php

declare(strict_types=1);

/**
 * Description of MetricsHttpClient.php
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Http;

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
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Support\ResponseField;

class MetricsHttpClient implements MetricsClient
{
    private const string LIST_METRICS_URL_TEMPLATE = 'api/v1/accounts/%s/metrics';

    private const string QUERY_METRICS_URL_TEMPLATE = 'api/v1/accounts/%s/metrics/query';

    private const string LIST_DIMENSIONS_URL_TEMPLATE = 'api/v1/accounts/%s/dimensions';

    private const string LIST_SCOPES_URL_TEMPLATE = 'api/v1/accounts/%s/scopes';

    private const string SHOW_SNAPSHOT_URL_TEMPLATE = 'api/v1/accounts/%s/snapshots/%s/%s';

    public function __construct(
        private readonly ReportsHttpTransport $transport,
    ) {
    }

    public function listMetrics(string $accountId, ?ReportLocale $locale = null): array
    {
        $path = $this->transport->buildPath(self::LIST_METRICS_URL_TEMPLATE, $accountId);
        $response = $this->transport->getJson($path, $this->buildLocaleQuery($locale));

        $metrics = [];
        foreach (ResponseField::listOfObjects($response, 'data') as $metricData) {
            $metrics[] = MetricCatalogueEntryDTO::fromResponse($metricData);
        }

        return $metrics;
    }

    public function listDimensions(string $accountId, ?ReportLocale $locale = null): array
    {
        $path = $this->transport->buildPath(self::LIST_DIMENSIONS_URL_TEMPLATE, $accountId);
        $response = $this->transport->getJson($path, $this->buildLocaleQuery($locale));

        $dimensions = [];
        foreach (ResponseField::listOfObjects($response, 'data') as $dimensionData) {
            $dimensions[] = DimensionDTO::fromResponse($dimensionData);
        }

        return $dimensions;
    }

    public function listScopes(string $accountId, ?string $cursor = null, ?int $limit = null): ScopesPageDTO
    {
        $path = $this->transport->buildPath(self::LIST_SCOPES_URL_TEMPLATE, $accountId);
        $query = [];
        if ($cursor !== null) {
            $query['cursor'] = $cursor;
        }

        if ($limit !== null) {
            $query['limit'] = $limit;
        }

        return ScopesPageDTO::fromResponse($this->transport->getJson($path, $query));
    }

    public function queryMetrics(string $accountId, QueryMetricsRequestDTO $request): QueryMetricsResultDTO
    {
        $path = $this->transport->buildPath(self::QUERY_METRICS_URL_TEMPLATE, $accountId);
        $response = $this->transport->postJson($path, $request->toRequestBody());

        return QueryMetricsResultDTO::fromResponse($response);
    }

    public function getSnapshot(string $accountId, Grain $grain, string $periodKey, ?ScopeDTO $scope = null): SnapshotDTO
    {
        $path = $this->transport->buildPath(self::SHOW_SNAPSHOT_URL_TEMPLATE, $accountId, $grain->value, $periodKey);
        $query = [];
        if ($scope !== null) {
            $query['scope_type'] = $scope->getScopeTypeValue();
            $query['scope_id'] = $scope->getScopeId();
        }

        $response = $this->transport->getJson($path, $query);

        return SnapshotDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    /**
     * @return array<string, string>
     */
    private function buildLocaleQuery(?ReportLocale $locale): array
    {
        if ($locale === null) {
            return [];
        }

        return ['locale' => $locale->value];
    }
}
