<?php

declare(strict_types=1);

/**
 * Description of SuccessReportRunsStubClient.php
 *
 * Answers every runs call with StubResponses data, no HTTP. The PDF is a few stub bytes.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Success;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Contracts\ReportRunsClient;
use Dotsplatform\ReportsApi\DTO\Runs\CreatedReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\CreateReportRunRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\ReportPeriodRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ShareLinkDTO;

class SuccessReportRunsStubClient implements ReportRunsClient
{
    public function list(string $accountId, string $definitionId): array
    {
        return [ReportRunDTO::fromResponse(StubResponses::reportRun())];
    }

    public function create(string $accountId, string $definitionId, CreateReportRunRequestDTO $request, ?string $actorId = null): CreatedReportRunDTO
    {
        return CreatedReportRunDTO::fromResponse(StubResponses::createdReportRun());
    }

    public function get(string $accountId, string $definitionId, string $runId): ReportRunDTO
    {
        return ReportRunDTO::fromResponse(StubResponses::reportRun($runId, 'pdf_ready'));
    }

    public function downloadPdf(string $accountId, string $definitionId, string $runId): string
    {
        return StubResponses::PDF_BYTES;
    }

    public function createShareLink(string $accountId, string $definitionId, ReportPeriodRequestDTO $request, ?string $actorId = null): ShareLinkDTO
    {
        return ShareLinkDTO::fromResponse(StubResponses::shareLink());
    }
}
