<?php

declare(strict_types=1);

/**
 * Description of FailReportDefinitionsStubClient.php
 *
 * Every report definitions call throws ReportsServerException (503).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Contracts\ReportDefinitionsClient;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDataDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDefinitionDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDataRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDefinitionFormDTO;

class FailReportDefinitionsStubClient implements ReportDefinitionsClient
{
    public function list(string $accountId): array
    {
        throw StubFailure::make();
    }

    public function create(string $accountId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO
    {
        throw StubFailure::make();
    }

    public function get(string $accountId, string $definitionId): ReportDefinitionDTO
    {
        throw StubFailure::make();
    }

    public function update(string $accountId, string $definitionId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO
    {
        throw StubFailure::make();
    }

    public function delete(string $accountId, string $definitionId, ?string $actorId = null): void
    {
        throw StubFailure::make();
    }

    public function duplicate(string $accountId, string $definitionId, ?ReportDefinitionFormDTO $form = null, ?string $actorId = null): ReportDefinitionDTO
    {
        throw StubFailure::make();
    }

    public function resetToTemplate(string $accountId, string $definitionId, ?string $actorId = null): ReportDefinitionDTO
    {
        throw StubFailure::make();
    }

    public function getData(string $accountId, string $definitionId, ?ReportDataRequestDTO $request = null): ReportDataDTO
    {
        throw StubFailure::make();
    }

    public function listBlocks(string $accountId): array
    {
        throw StubFailure::make();
    }
}
