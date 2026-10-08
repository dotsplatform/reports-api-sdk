<?php

declare(strict_types=1);

/**
 * Description of ReportRunsHttpClient.php
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Http;

use Dotsplatform\ReportsApi\Contracts\ReportRunsClient;
use Dotsplatform\ReportsApi\DTO\Runs\CreatedReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ReportRunDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\CreateReportRunRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\ReportPeriodRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\ShareLinkDTO;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ReportRunsHttpClient implements ReportRunsClient
{
    private const string RUNS_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s/runs';

    private const string RUN_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s/runs/%s';

    private const string RUN_PDF_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s/runs/%s/pdf';

    private const string SHARE_LINKS_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s/share-links';

    public function __construct(
        private readonly ReportsHttpTransport $transport,
    ) {
    }

    public function list(string $accountId, string $definitionId): array
    {
        $path = $this->transport->buildPath(self::RUNS_URL_TEMPLATE, $accountId, $definitionId);
        $response = $this->transport->getJson($path);

        $runs = [];
        foreach (ResponseField::listOfObjects($response, 'data') as $runData) {
            $runs[] = ReportRunDTO::fromResponse($runData);
        }

        return $runs;
    }

    public function create(string $accountId, string $definitionId, CreateReportRunRequestDTO $request, ?string $actorId = null): CreatedReportRunDTO
    {
        $path = $this->transport->buildPath(self::RUNS_URL_TEMPLATE, $accountId, $definitionId);
        $response = $this->transport->postJson($path, $request->toRequestBody(), $actorId);

        return CreatedReportRunDTO::fromResponse($response);
    }

    public function get(string $accountId, string $definitionId, string $runId): ReportRunDTO
    {
        $path = $this->transport->buildPath(self::RUN_URL_TEMPLATE, $accountId, $definitionId, $runId);
        $response = $this->transport->getJson($path);

        return ReportRunDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function downloadPdf(string $accountId, string $definitionId, string $runId): string
    {
        $path = $this->transport->buildPath(self::RUN_PDF_URL_TEMPLATE, $accountId, $definitionId, $runId);

        return $this->transport->getBinary($path);
    }

    public function createShareLink(string $accountId, string $definitionId, ReportPeriodRequestDTO $request, ?string $actorId = null): ShareLinkDTO
    {
        $path = $this->transport->buildPath(self::SHARE_LINKS_URL_TEMPLATE, $accountId, $definitionId);
        $response = $this->transport->postJson($path, $request->toRequestBody(), $actorId);

        return ShareLinkDTO::fromResponse(ResponseField::array($response, 'data'));
    }
}
