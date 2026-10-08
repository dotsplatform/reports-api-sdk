<?php

declare(strict_types=1);

/**
 * Description of SuccessReportsStubClient.php
 *
 * A ReportsClient for tests of consumers: every call succeeds with sample data (Stubs\StubResponses), no HTTP.
 * Bind it in a test: $this->app->bind(ReportsClient::class, SuccessReportsStubClient::class).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\Success\SuccessApiClientsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Success\SuccessMetricsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Success\SuccessOperationsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Success\SuccessReportDefinitionsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Success\SuccessReportRunsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Success\SuccessSettingsStubClient;
use Dotsplatform\ReportsApi\Clients\Stubs\Success\SuccessSubscriptionsStubClient;
use Dotsplatform\ReportsApi\Contracts\ApiClientsClient;
use Dotsplatform\ReportsApi\Contracts\MetricsClient;
use Dotsplatform\ReportsApi\Contracts\OperationsClient;
use Dotsplatform\ReportsApi\Contracts\ReportDefinitionsClient;
use Dotsplatform\ReportsApi\Contracts\ReportRunsClient;
use Dotsplatform\ReportsApi\Contracts\SettingsClient;
use Dotsplatform\ReportsApi\Contracts\SubscriptionsClient;
use Dotsplatform\ReportsApi\ReportsClient;

class SuccessReportsStubClient implements ReportsClient
{
    public function metrics(): MetricsClient
    {
        return new SuccessMetricsStubClient();
    }

    public function reportDefinitions(): ReportDefinitionsClient
    {
        return new SuccessReportDefinitionsStubClient();
    }

    public function reportRuns(): ReportRunsClient
    {
        return new SuccessReportRunsStubClient();
    }

    public function settings(): SettingsClient
    {
        return new SuccessSettingsStubClient();
    }

    public function subscriptions(): SubscriptionsClient
    {
        return new SuccessSubscriptionsStubClient();
    }

    public function apiClients(): ApiClientsClient
    {
        return new SuccessApiClientsStubClient();
    }

    public function operations(): OperationsClient
    {
        return new SuccessOperationsStubClient();
    }
}
