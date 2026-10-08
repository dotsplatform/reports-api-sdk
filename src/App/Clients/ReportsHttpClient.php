<?php

declare(strict_types=1);

/**
 * Description of ReportsHttpClient.php
 *
 * The real ReportsClient: every group shares one transport, so every call carries X-Internal-Gateway-Token.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients;

use Dotsplatform\ReportsApi\Clients\Http\ApiClientsHttpClient;
use Dotsplatform\ReportsApi\Clients\Http\MetricsHttpClient;
use Dotsplatform\ReportsApi\Clients\Http\OperationsHttpClient;
use Dotsplatform\ReportsApi\Clients\Http\ReportDefinitionsHttpClient;
use Dotsplatform\ReportsApi\Clients\Http\ReportRunsHttpClient;
use Dotsplatform\ReportsApi\Clients\Http\SettingsHttpClient;
use Dotsplatform\ReportsApi\Contracts\ApiClientsClient;
use Dotsplatform\ReportsApi\Contracts\MetricsClient;
use Dotsplatform\ReportsApi\Contracts\OperationsClient;
use Dotsplatform\ReportsApi\Contracts\ReportDefinitionsClient;
use Dotsplatform\ReportsApi\Contracts\ReportRunsClient;
use Dotsplatform\ReportsApi\Contracts\SettingsClient;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\ReportsClient;

class ReportsHttpClient implements ReportsClient
{
    private readonly MetricsClient $metricsClient;

    private readonly ReportDefinitionsClient $reportDefinitionsClient;

    private readonly ReportRunsClient $reportRunsClient;

    private readonly SettingsClient $settingsClient;

    private readonly ApiClientsClient $apiClientsClient;

    private readonly OperationsClient $operationsClient;

    public function __construct(ReportsHttpTransport $transport)
    {
        $this->metricsClient = new MetricsHttpClient($transport);
        $this->reportDefinitionsClient = new ReportDefinitionsHttpClient($transport);
        $this->reportRunsClient = new ReportRunsHttpClient($transport);
        $this->settingsClient = new SettingsHttpClient($transport);
        $this->apiClientsClient = new ApiClientsHttpClient($transport);
        $this->operationsClient = new OperationsHttpClient($transport);
    }

    public function metrics(): MetricsClient
    {
        return $this->metricsClient;
    }

    public function reportDefinitions(): ReportDefinitionsClient
    {
        return $this->reportDefinitionsClient;
    }

    public function reportRuns(): ReportRunsClient
    {
        return $this->reportRunsClient;
    }

    public function settings(): SettingsClient
    {
        return $this->settingsClient;
    }

    public function apiClients(): ApiClientsClient
    {
        return $this->apiClientsClient;
    }

    public function operations(): OperationsClient
    {
        return $this->operationsClient;
    }
}
