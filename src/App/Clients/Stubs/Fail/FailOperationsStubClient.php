<?php

declare(strict_types=1);

/**
 * Description of FailOperationsStubClient.php
 *
 * Every operations call throws ReportsServerException (503).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Contracts\OperationsClient;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\BackfillRequestDTO;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\ReaggregationRequestDTO;

class FailOperationsStubClient implements OperationsClient
{
    public function listTemplates(): array
    {
        throw StubFailure::make();
    }

    public function queueBackfill(string $accountId, BackfillRequestDTO $request, ?string $actorId = null): int
    {
        throw StubFailure::make();
    }

    public function queueReaggregation(ReaggregationRequestDTO $request, ?string $actorId = null): string
    {
        throw StubFailure::make();
    }
}
