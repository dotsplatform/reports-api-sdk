<?php

declare(strict_types=1);

/**
 * Description of ReportDefinitionsClient.php
 *
 * report-definitions CRUD, duplicate, reset-to-template, data, and GET blocks. Write calls take the optional
 * X-Actor-Id (the livesite user acting) for the audit log.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Contracts;

use Dotsplatform\ReportsApi\DTO\Reports\BlockTypeDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDataDTO;
use Dotsplatform\ReportsApi\DTO\Reports\ReportDefinitionDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDataRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDefinitionFormDTO;

interface ReportDefinitionsClient
{
    /**
     * The account own definitions first, then its plan templates.
     *
     * @return list<ReportDefinitionDTO>
     */
    public function list(string $accountId): array;

    public function create(string $accountId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO;

    public function get(string $accountId, string $definitionId): ReportDefinitionDTO;

    /**
     * Only the set fields change. A template answers 404.
     */
    public function update(string $accountId, string $definitionId, ReportDefinitionFormDTO $form, ?string $actorId = null): ReportDefinitionDTO;

    /**
     * Soft delete. A template answers 404.
     */
    public function delete(string $accountId, string $definitionId, ?string $actorId = null): void;

    /**
     * A copy of a definition or template; the form overrides fields of the copy (e.g. its name).
     */
    public function duplicate(string $accountId, string $definitionId, ?ReportDefinitionFormDTO $form = null, ?string $actorId = null): ReportDefinitionDTO;

    /**
     * Puts the template layout back; the name is kept.
     */
    public function resetToTemplate(string $accountId, string $definitionId, ?string $actorId = null): ReportDefinitionDTO;

    /**
     * The resolved data of every visible block, in layout order.
     */
    public function getData(string $accountId, string $definitionId, ?ReportDataRequestDTO $request = null): ReportDataDTO;

    /**
     * Block types with their config schemas, for the layout editor.
     *
     * @return list<BlockTypeDTO>
     */
    public function listBlocks(string $accountId): array;
}
