<?php

declare(strict_types=1);

/**
 * Description of FailReportsStubClient.php
 *
 * A ReportsClient for tests of consumers: every call throws ReportsServerException (503), as when the reports
 * service is down. Bind it in a test: $this->app->bind(ReportsClient::class, FailReportsStubClient::class).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\Fail\FailApiClientsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Fail\FailMetricsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Fail\FailOperationsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Fail\FailReportDefinitionsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Fail\FailReportRunsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Fail\FailSettingsStubClient;
use Dotsplatform\ReportsApi\Contracts\ApiClientsClient;
use Dotsplatform\ReportsApi\Contracts\MetricsClient;
use Dotsplatform\ReportsApi\Contracts\OperationsClient;
use Dotsplatform\ReportsApi\Contracts\ReportDefinitionsClient;
use Dotsplatform\ReportsApi\Contracts\ReportRunsClient;
use Dotsplatform\ReportsApi\Contracts\SettingsClient;
use Dotsplatform\ReportsApi\ReportsClient;

class FailReportsStubClient implements ReportsClient
{
    public function metrics(): MetricsClient
    {
        return new FailMetricsStubClient();
    }

    public function reportDefinitions(): ReportDefinitionsClient
    {
        return new FailReportDefinitionsStubClient();
    }

    public function reportRuns(): ReportRunsClient
    {
        return new FailReportRunsStubClient();
    }

    public function settings(): SettingsClient
    {
        return new FailSettingsStubClient();
    }

    public function apiClients(): ApiClientsClient
    {
        return new FailApiClientsStubClient();
    }

    public function operations(): OperationsClient
    {
        return new FailOperationsStubClient();
    }
}
