<?php

declare(strict_types=1);

/**
 * Description of OperationsHttpClientTest.php
 *
 * operations(): platform templates, backfill of one account and re-aggregation.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\BackfillRequestDTO;
use Dotsplatform\ReportsApi\DTO\Operations\Requests\ReaggregationRequestDTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class OperationsHttpClientTest extends HttpClientTestCase
{
    public function testListsTemplates(): void
    {
        $template = StubResponses::reportDefinition();
        $template['is_template'] = true;
        $this->queueJsonResponse(['data' => [$template]]);

        $templates = $this->makeClient()->operations()->listTemplates();

        $this->assertLastRequest('GET', '/api/v1/templates');
        $this->assertTrue($templates[0]->isTemplate());
    }

    public function testQueuesABackfill(): void
    {
        $this->queueJsonResponse(['data' => ['queued_chunks' => 4]], 202);

        $queuedChunks = $this->makeClient()->operations()->queueBackfill(StubResponses::ACCOUNT_ID, BackfillRequestDTO::fromArray([
            'fromDate' => '2026-09-01',
            'toDate' => '2026-09-30',
        ]), self::ACTOR_ID);

        $this->assertLastRequest('POST', '/api/v1/accounts/'.StubResponses::ACCOUNT_ID.'/backfill');
        $this->assertSame(['from' => '2026-09-01', 'to' => '2026-09-30'], $this->getLastRequestJson());
        $this->assertSame(4, $queuedChunks);
    }

    public function testQueuesAReaggregation(): void
    {
        $this->queueJsonResponse(['data' => ['status' => 'queued']], 202);
        $this->queueJsonResponse(['data' => ['status' => 'queued']], 202);

        $status = $this->makeClient()->operations()->queueReaggregation(ReaggregationRequestDTO::fromArray([
            'accountIds' => [StubResponses::ACCOUNT_ID],
            'fromDate' => '2026-09-01',
            'toDate' => '2026-09-30',
            'grains' => [Grain::DAY, Grain::MONTH],
        ]));

        $this->assertLastRequest('POST', '/api/v1/reaggregate');
        $this->assertSame([
            'accounts' => [StubResponses::ACCOUNT_ID],
            'from' => '2026-09-01',
            'to' => '2026-09-30',
            'grains' => ['day', 'month'],
        ], $this->getLastRequestJson());
        $this->assertSame('queued', $status);

        $this->assertSame('queued', $this->makeClient()->operations()->queueReaggregation(ReaggregationRequestDTO::empty()));
        $this->assertSame('{}', $this->getLastRequestRawBody());
    }
}
