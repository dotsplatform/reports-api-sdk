<?php

declare(strict_types=1);

/**
 * Description of OperationsClient.php
 *
 * Staff operations (internal token only): platform templates, backfill of one account, re-aggregation.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Contracts;

use Dotsplatform\ReportsApi\DTO\Operations\Requests\BackfillRequestDTO;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\ReaggregationRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDefinitionDTO;

interface OperationsClient
{
    /**
     * @return list<ReportDefinitionDTO>
     */
    public function listTemplates(): array;

    /**
     * @return int the number of queued backfill chunks
     */
    public function queueBackfill(string $accountId, BackfillRequestDTO $request, ?string $actorId = null): int;

    /**
     * The service queues the fan-out in one job and answers right away.
     *
     * @return string the queue status ("queued")
     */
    public function queueReaggregation(ReaggregationRequestDTO $request, ?string $actorId = null): string;
}
