<?php

declare(strict_types=1);

/**
 * Description of SuccessOperationsStubClient.php
 *
 * Answers every operations call with StubResponses data, no HTTP: one template, one queued chunk or job.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Success;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Contracts\OperationsClient;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\BackfillRequestDTO;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\ReaggregationRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDefinitionDTO;

class SuccessOperationsStubClient implements OperationsClient
{
    private const int QUEUED_COUNT = 1;

    public function listTemplates(): array
    {
        return [ReportDefinitionDTO::fromResponse(StubResponses::reportDefinition())];
    }

    public function queueBackfill(string $accountId, BackfillRequestDTO $request, ?string $actorId = null): int
    {
        return self::QUEUED_COUNT;
    }

    public function queueReaggregation(ReaggregationRequestDTO $request, ?string $actorId = null): int
    {
        return self::QUEUED_COUNT;
    }
}
