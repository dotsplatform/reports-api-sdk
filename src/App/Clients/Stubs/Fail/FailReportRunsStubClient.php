<?php

declare(strict_types=1);

/**
 * Description of FailReportRunsStubClient.php
 *
 * Every runs call throws ReportsServerException (503).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Contracts\ReportRunsClient;
use Dotsplatform\ReportsApi\DTO\Runs\CreatedReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\CreateReportRunRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\ReportPeriodRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ShareLinkDTO;

class FailReportRunsStubClient implements ReportRunsClient
{
    public function list(string $accountId, string $definitionId): array
    {
        throw StubFailure::make();
    }

    public function create(string $accountId, string $definitionId, CreateReportRunRequestDTO $request, ?string $actorId = null): CreatedReportRunDTO
    {
        throw StubFailure::make();
    }

    public function get(string $accountId, string $definitionId, string $runId): ReportRunDTO
    {
        throw StubFailure::make();
    }

    public function downloadPdf(string $accountId, string $definitionId, string $runId): string
    {
        throw StubFailure::make();
    }

    public function createShareLink(string $accountId, string $definitionId, ReportPeriodRequestDTO $request, ?string $actorId = null): ShareLinkDTO
    {
        throw StubFailure::make();
    }

    public function regenerate(string $accountId, string $definitionId, string $runId, ?string $actorId = null): ReportRunDTO
    {
        throw StubFailure::make();
    }
}
