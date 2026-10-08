<?php

declare(strict_types=1);

/**
 * Description of ReportsClient.php
 *
 * The reports service SDK entry point. Resolve it from the container (ReportsApiServiceProvider binds the HTTP
 * client; tests bind SuccessReportsStubClient or FailReportsStubClient) and pick a group:
 *
 *     $reportsClient->reportDefinitions()->create($accountId, $form, $actorId);
 *
 * Every call may throw a ReportsApiException subclass (404, 422, 401, 403, 429, server or connection errors).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi;

use Dotsplatform\ReportsApi\Contracts\ApiClientsClient;
use Dotsplatform\ReportsApi\Contracts\MetricsClient;
use Dotsplatform\ReportsApi\Contracts\OperationsClient;
use Dotsplatform\ReportsApi\Contracts\ReportDefinitionsClient;
use Dotsplatform\ReportsApi\Contracts\ReportRunsClient;
use Dotsplatform\ReportsApi\Contracts\SettingsClient;

interface ReportsClient
{
    /**
     * Metric catalogue, dimensions, scopes, metric queries and raw snapshots.
     */
    public function metrics(): MetricsClient;

    /**
     * Report definitions CRUD, their resolved data and the block catalogue.
     */
    public function reportDefinitions(): ReportDefinitionsClient;

    /**
     * Runs of a report definition (frozen renders, PDF) and share links.
     */
    public function reportRuns(): ReportRunsClient;

    /**
     * The account report settings.
     */
    public function settings(): SettingsClient;

    /**
     * Account API tokens (internal callers only).
     */
    public function apiClients(): ApiClientsClient;

    /**
     * Staff operations: templates, backfill, re-aggregation (internal callers only).
     */
    public function operations(): OperationsClient;
}
