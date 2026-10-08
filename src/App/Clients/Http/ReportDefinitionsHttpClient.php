<?php

declare(strict_types=1);

/**
 * Description of ReportDefinitionsHttpClient.php
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Http;

use Dotsplatform\ReportsApi\Contracts\ReportDefinitionsClient;
use Dotsplatform\ReportsApi\DTO\Reports\BlockTypeDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDataDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDefinitionDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDataRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDefinitionFormDTO;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ReportDefinitionsHttpClient implements ReportDefinitionsClient
{
    private const string DEFINITIONS_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions';

    private const string DEFINITION_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s';

    private const string DUPLICATE_DEFINITION_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s/duplicate';

    private const string RESET_DEFINITION_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s/reset-to-template';

    private const string DEFINITION_DATA_URL_TEMPLATE = 'api/v1/accounts/%s/report-definitions/%s/data';

    private const string LIST_BLOCKS_URL_TEMPLATE = 'api/v1/accounts/%s/blocks';

    public function __construct(
        private readonly ReportsHttpTransport $transport,
    ) {
    }

    public function list(string $accountId): array
    {
        $path = $this->transport->buildPath(self::DEFINITIONS_URL_TEMPLATE, $accountId);
        $response = $this->transport->getJson($path);

        return ReportDefinitionDTO::listFromResponse(ResponseField::listOfObjects($response, 'data'));
    }

    public function create(string $accountId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO
    {
        $path = $this->transport->buildPath(self::DEFINITIONS_URL_TEMPLATE, $accountId);
        $response = $this->transport->postJson($path, $form->toRequestBody(), $actorId);

        return ReportDefinitionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function get(string $accountId, string $definitionId): ReportDefinitionDTO
    {
        $path = $this->transport->buildPath(self::DEFINITION_URL_TEMPLATE, $accountId, $definitionId);
        $response = $this->transport->getJson($path);

        return ReportDefinitionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function update(string $accountId, string $definitionId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO
    {
        $path = $this->transport->buildPath(self::DEFINITION_URL_TEMPLATE, $accountId, $definitionId);
        $response = $this->transport->patchJson($path, $form->toRequestBody(), $actorId);

        return ReportDefinitionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function delete(string $accountId, string $definitionId, ?string $actorId = null): void
    {
        $path = $this->transport->buildPath(self::DEFINITION_URL_TEMPLATE, $accountId, $definitionId);
        $this->transport->delete($path, $actorId);
    }

    public function duplicate(string $accountId, string $definitionId, ?ReportDefinitionFormDTO $form = null, ?string $actorId = null): ReportDefinitionDTO
    {
        $path = $this->transport->buildPath(self::DUPLICATE_DEFINITION_URL_TEMPLATE, $accountId, $definitionId);
        $body = [];
        if ($form !== null) {
            $body = $form->toRequestBody();
        }

        $response = $this->transport->postJson($path, $body, $actorId);

        return ReportDefinitionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function resetToTemplate(string $accountId, string $definitionId, ?string $actorId = null): ReportDefinitionDTO
    {
        $path = $this->transport->buildPath(self::RESET_DEFINITION_URL_TEMPLATE, $accountId, $definitionId);
        $response = $this->transport->postJson($path, [], $actorId);

        return ReportDefinitionDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function getData(string $accountId, string $definitionId, ?ReportDataRequestDTO $request = null): ReportDataDTO
    {
        $path = $this->transport->buildPath(self::DEFINITION_DATA_URL_TEMPLATE, $accountId, $definitionId);
        $query = [];
        if ($request !== null) {
            $query = $request->toQuery();
        }

        $response = $this->transport->getJson($path, $query);

        return ReportDataDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function listBlocks(string $accountId): array
    {
        $path = $this->transport->buildPath(self::LIST_BLOCKS_URL_TEMPLATE, $accountId);
        $response = $this->transport->getJson($path);

        $blocks = [];
        foreach (ResponseField::listOfObjects($response, 'data') as $blockData) {
            $blocks[] = BlockTypeDTO::fromResponse($blockData);
        }

        return $blocks;
    }
}
