<?php

declare(strict_types=1);

/**
 * Description of OperationsHttpClient.php
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Http;

use Dotsplatform\ReportsApi\Contracts\OperationsClient;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\BackfillRequestDTO;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\ReaggregationRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDefinitionDTO;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Support\ResponseField;

class OperationsHttpClient implements OperationsClient
{
    private const string LIST_TEMPLATES_URL = 'api/v1/templates';

    private const string QUEUE_BACKFILL_URL_TEMPLATE = 'api/v1/accounts/%s/backfill';

    private const string QUEUE_REAGGREGATION_URL = 'api/v1/reaggregate';

    public function __construct(
        private readonly ReportsHttpTransport $transport,
    ) {
    }

    public function listTemplates(): array
    {
        $response = $this->transport->getJson(self::LIST_TEMPLATES_URL);

        return ReportDefinitionDTO::listFromResponse(ResponseField::listOfObjects($response, 'data'));
    }

    public function queueBackfill(string $accountId, BackfillRequestDTO $request, ?string $actorId = null): int
    {
        $path = $this->transport->buildPath(self::QUEUE_BACKFILL_URL_TEMPLATE, $accountId);
        $response = $this->transport->postJson($path, $request->toRequestBody(), $actorId);

        return ResponseField::int(ResponseField::array($response, 'data'), 'queued_chunks');
    }

    public function queueReaggregation(ReaggregationRequestDTO $request, ?string $actorId = null): string
    {
        $response = $this->transport->postJson(self::QUEUE_REAGGREGATION_URL, $request->toRequestBody(), $actorId);

        return ResponseField::string(ResponseField::array($response, 'data'), 'status');
    }
}
