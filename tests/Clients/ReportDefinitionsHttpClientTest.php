<?php

declare(strict_types=1);

/**
 * Description of ReportDefinitionsHttpClientTest.php
 *
 * reportDefinitions(): list, create from a template with the actor, PATCH with only the set fields and a layout,
 * delete, duplicate, reset, block data and the block catalogue.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\DTO\Reports\LayoutBlockDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDataRequestDTO;
use Dotsplatform\ReportsApi\DTO\Reports\Requests\ReportDefinitionFormDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportPurpose;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Http\RequestHeader;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class ReportDefinitionsHttpClientTest extends HttpClientTestCase
{
    private const string ACCOUNT = StubResponses::ACCOUNT_ID;

    private const string DEFINITION = StubResponses::DEFINITION_ID;

    private const string DEFINITIONS_PATH = '/api/v1/accounts/'.self::ACCOUNT.'/report-definitions';

    public function testListsDefinitionsAndTemplates(): void
    {
        $template = StubResponses::reportDefinition('0199a000-0000-7000-8000-0000000d0002', 'Monthly owner report (PRO)');
        $template['is_template'] = true;
        $this->queueJsonResponse(['data' => [StubResponses::reportDefinition(), $template]]);

        $definitions = $this->makeClient()->reportDefinitions()->list(self::ACCOUNT);

        $this->assertLastRequest('GET', self::DEFINITIONS_PATH);
        $this->assertCount(2, $definitions);
        $this->assertFalse($definitions[0]->isTemplate());
        $this->assertTrue($definitions[1]->isTemplate());
    }

    public function testCreatesFromATemplateWithTheActor(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::reportDefinition(self::DEFINITION, 'Weekly for owners')], 201);

        $definition = $this->makeClient()->reportDefinitions()->create(self::ACCOUNT, ReportDefinitionFormDTO::fromArray([
            'templateKey' => 'weekly',
            'name' => 'Weekly for owners',
            'isDefault' => true,
        ]), self::ACTOR_ID);

        $this->assertLastRequest('POST', self::DEFINITIONS_PATH);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));
        $this->assertSame(['template_key' => 'weekly', 'name' => 'Weekly for owners', 'is_default' => true], $this->getLastRequestJson());

        $this->assertSame(self::DEFINITION, $definition->getId());
        $this->assertSame('Weekly for owners', $definition->getName());
        $this->assertSame('pro_monthly', $definition->getTemplateKey());
        $this->assertSame(ReportPurpose::OWNER, $definition->getPurpose());
        $this->assertSame(ScopeType::ACCOUNT, $definition->getScope()->getScopeType());
        $this->assertNull($definition->getScope()->getScopeId());
        $this->assertSame(Grain::MONTH, $definition->getDefaultGrain());
        $this->assertSame(CompareMode::PREVIOUS_PERIOD, $definition->getComparison());
        $this->assertSame(6, $definition->getTrendPeriods());
        $this->assertSame(90, $definition->getShareLinkDays());
        $this->assertTrue($definition->isDefault());
        $this->assertSame('livesite-user-7', $definition->getCreatedBy());
        $this->assertSame('kpi_group', $definition->getBlocks()[1]->getType());
        $this->assertSame(['group' => 'general'], $definition->getBlocks()[1]->getConfig());
        $this->assertSame([], $definition->getBlocks()[0]->getConfig());
    }

    public function testUpdateSendsOnlyTheSetFieldsAndTheLayout(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::reportDefinition(self::DEFINITION, 'Short')]);

        $this->makeClient()->reportDefinitions()->update(self::ACCOUNT, self::DEFINITION, ReportDefinitionFormDTO::fromArray([
            'name' => 'Short',
            'scopeType' => ScopeType::CITY,
            'scopeId' => StubResponses::CITY_ID,
            'trendPeriods' => 9,
            'blocks' => [LayoutBlockDTO::create('header'), LayoutBlockDTO::create('top_table', ['entity' => 'item', 'limit' => 20])],
        ]), self::ACTOR_ID);

        $this->assertLastRequest('PATCH', self::DEFINITIONS_PATH.'/'.self::DEFINITION);
        $this->assertSame([
            'name' => 'Short',
            'scope_type' => 'city',
            'scope_id' => StubResponses::CITY_ID,
            'trend_periods' => 9,
            'layout' => ['blocks' => [
                ['type' => 'header', 'config' => []],
                ['type' => 'top_table', 'config' => ['entity' => 'item', 'limit' => 20]],
            ]],
        ], $this->getLastRequestJson());
        // An empty block config must be a JSON object for the service.
        $this->assertStringContainsString('{"type":"header","config":{}}', $this->getLastRequestRawBody());
    }

    public function testDeletesWithTheActor(): void
    {
        $this->queueNoContentResponse();

        $this->makeClient()->reportDefinitions()->delete(self::ACCOUNT, self::DEFINITION, self::ACTOR_ID);

        $this->assertLastRequest('DELETE', self::DEFINITIONS_PATH.'/'.self::DEFINITION);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));
    }

    public function testGetsDuplicatesAndResets(): void
    {
        $copyId = '0199a000-0000-7000-8000-0000000d0003';
        $this->queueJsonResponse(['data' => StubResponses::reportDefinition()]);
        $this->queueJsonResponse(['data' => StubResponses::reportDefinition($copyId, 'Copy')], 201);
        $this->queueJsonResponse(['data' => StubResponses::reportDefinition($copyId, 'Copy')], 201);
        $this->queueJsonResponse(['data' => StubResponses::reportDefinition()]);
        $reportDefinitions = $this->makeClient()->reportDefinitions();

        $this->assertSame(self::DEFINITION, $reportDefinitions->get(self::ACCOUNT, self::DEFINITION)->getId());
        $this->assertLastRequest('GET', self::DEFINITIONS_PATH.'/'.self::DEFINITION);

        $copy = $reportDefinitions->duplicate(self::ACCOUNT, self::DEFINITION, ReportDefinitionFormDTO::fromArray(['name' => 'Copy']));
        $this->assertLastRequest('POST', self::DEFINITIONS_PATH.'/'.self::DEFINITION.'/duplicate');
        $this->assertSame(['name' => 'Copy'], $this->getLastRequestJson());
        $this->assertSame($copyId, $copy->getId());

        $reportDefinitions->duplicate(self::ACCOUNT, self::DEFINITION);
        $this->assertSame('{}', $this->getLastRequestRawBody());

        $reportDefinitions->resetToTemplate(self::ACCOUNT, self::DEFINITION, self::ACTOR_ID);
        $this->assertLastRequest('POST', self::DEFINITIONS_PATH.'/'.self::DEFINITION.'/reset-to-template');
    }

    public function testGetsTheDataOfEveryBlock(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::reportData()]);

        $report = $this->makeClient()->reportDefinitions()->getData(self::ACCOUNT, self::DEFINITION, ReportDataRequestDTO::fromArray([
            'scope' => ScopeDTO::create(ScopeType::CITY, StubResponses::CITY_ID),
            'grain' => Grain::MONTH,
            'periodKey' => '2026-09',
            'compare' => CompareMode::SAME_PERIOD_LAST_YEAR,
        ]));

        $this->assertLastRequest('GET', self::DEFINITIONS_PATH.'/'.self::DEFINITION.'/data');
        $this->assertSame([
            'scope_type' => 'city',
            'scope_id' => StubResponses::CITY_ID,
            'grain' => 'month',
            'period' => '2026-09',
            'compare' => 'same_period_last_year',
        ], $this->getLastRequestQuery());
        $this->assertSame('Monthly owner report', $report->getName());
        $this->assertSame('Real Roll', $report->getScopeName());
        $this->assertSame('en', $report->getLocale());
        $this->assertSame('September 2026', $report->getPeriod()->getLabel());
        $this->assertSame('August 2026', $report->getComparedWith());
        $this->assertSame(StubResponses::CREATED_AT, $report->getDataAsOf());
        $this->assertSame('header', $report->getBlocks()[0]->getType());
        $this->assertSame(['orders' => '2'], $report->getBlocks()[0]->getData());
    }

    public function testGetsTheDataOfTheDefaultPeriod(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::reportData()]);

        $this->makeClient()->reportDefinitions()->getData(self::ACCOUNT, self::DEFINITION);

        $this->assertSame([], $this->getLastRequestQuery());
    }

    public function testListsBlockTypes(): void
    {
        $this->queueJsonResponse(['data' => [StubResponses::blockType()]]);

        $blocks = $this->makeClient()->reportDefinitions()->listBlocks(self::ACCOUNT);

        $this->assertLastRequest('GET', '/api/v1/accounts/'.self::ACCOUNT.'/blocks');
        $this->assertSame('top_table', $blocks[0]->getType());
        $this->assertSame('venues', $blocks[0]->getFamily());
        $this->assertSame('integer', $blocks[0]->getConfigSchema()['limit'] ?? null);
    }
}
