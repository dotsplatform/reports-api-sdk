<?php

declare(strict_types=1);

/**
 * Description of ReportRunsClient.php
 *
 * report-definitions/{id}/runs (list, create, show, PDF download) and share-links.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Contracts;

use Dotsplatform\ReportsApi\DTO\Runs\CreatedReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\CreateReportRunRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\ReportPeriodRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ShareLinkDTO;

interface ReportRunsClient
{
    /**
     * @return list<ReportRunDTO>
     */
    public function list(string $accountId, string $definitionId): array;

    /**
     * Freezes the report for a period; optionally creates a share link and queues the PDF.
     */
    public function create(string $accountId, string $definitionId, CreateReportRunRequestDTO $request, ?string $actorId = null): CreatedReportRunDTO;

    public function get(string $accountId, string $definitionId, string $runId): ReportRunDTO;

    /**
     * The PDF bytes. Throws ReportsNotFoundException while the PDF is not ready (status pdf_pending).
     */
    public function downloadPdf(string $accountId, string $definitionId, string $runId): string;

    /**
     * A public link to the report for a period. The page is generated and stored when the link is made and shows the data as of then (`ReportRunDTO::getGeneratedAt()`); call `regenerate()` to refresh it.
     */
    public function createShareLink(string $accountId, string $definitionId, ReportPeriodRequestDTO $request, ?string $actorId = null): ShareLinkDTO;

    /**
     * Makes the run's stored files again with current data; the link stays.
     */
    public function regenerate(string $accountId, string $definitionId, string $runId, ?string $actorId = null): ReportRunDTO;
}
