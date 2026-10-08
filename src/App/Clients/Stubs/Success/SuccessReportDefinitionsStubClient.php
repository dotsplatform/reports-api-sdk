<?php

declare(strict_types=1);

/**
 * Description of SuccessReportDefinitionsStubClient.php
 *
 * Answers every report definitions call with StubResponses data, no HTTP. Created and changed definitions carry the
 * asked id and name.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Success;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Contracts\ReportDefinitionsClient;
use Dotsplatform\ReportsApi\DTO\Reports\BlockTypeDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDataDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDefinitionDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDataRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDefinitionFormDTO;

class SuccessReportDefinitionsStubClient implements ReportDefinitionsClient
{
    private const string DEFAULT_NAME = 'Monthly owner report';

    public function list(string $accountId): array
    {
        return [ReportDefinitionDTO::fromResponse(StubResponses::reportDefinition())];
    }

    public function create(string $accountId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO
    {
        return $this->makeDefinition(StubResponses::DEFINITION_ID, $form);
    }

    public function get(string $accountId, string $definitionId): ReportDefinitionDTO
    {
        return ReportDefinitionDTO::fromResponse(StubResponses::reportDefinition($definitionId));
    }

    public function update(string $accountId, string $definitionId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO
    {
        return $this->makeDefinition($definitionId, $form);
    }

    public function delete(string $accountId, string $definitionId, ?string $actorId = null): void
    {
    }

    public function duplicate(string $accountId, string $definitionId, ?ReportDefinitionFormDTO $form = null, ?string $actorId = null): ReportDefinitionDTO
    {
        return $this->makeDefinition(StubResponses::DEFINITION_ID, $form);
    }

    public function resetToTemplate(string $accountId, string $definitionId, ?string $actorId = null): ReportDefinitionDTO
    {
        return ReportDefinitionDTO::fromResponse(StubResponses::reportDefinition($definitionId));
    }

    public function getData(string $accountId, string $definitionId, ?ReportDataRequestDTO $request = null): ReportDataDTO
    {
        return ReportDataDTO::fromResponse(StubResponses::reportData());
    }

    public function listBlocks(string $accountId): array
    {
        return [BlockTypeDTO::fromResponse(StubResponses::blockType())];
    }

    private function makeDefinition(string $definitionId, ?ReportDefinitionFormDTO $form): ReportDefinitionDTO
    {
        return ReportDefinitionDTO::fromResponse(StubResponses::reportDefinition($definitionId, $this->readName($form)));
    }

    private function readName(?ReportDefinitionFormDTO $form): string
    {
        if ($form === null) {
            return self::DEFAULT_NAME;
        }

        $name = $form->getName();
        if ($name === null) {
            return self::DEFAULT_NAME;
        }

        return $name;
    }
}
