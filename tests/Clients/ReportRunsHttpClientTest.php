<?php

declare(strict_types=1);

/**
 * Description of ReportRunsHttpClientTest.php
 *
 * reportRuns(): create with a share link and a PDF, list, show, PDF bytes, share links.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\CreateReportRunRequestDTO;
use Dotsplatform\ReportsApi\DTO\Runs\Requests\ReportPeriodRequestDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportRunStatus;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Exceptions\ReportsNotFoundException;
use Dotsplatform\ReportsApi\Http\RequestHeader;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class ReportRunsHttpClientTest extends HttpClientTestCase
{
    private const string ACCOUNT = StubResponses::ACCOUNT_ID;

    private const string DEFINITION = StubResponses::DEFINITION_ID;

    private const string RUN = StubResponses::RUN_ID;

    private const string RUNS_PATH = '/api/v1/accounts/'.self::ACCOUNT.'/report-definitions/'.self::DEFINITION.'/runs';

    public function testCreatesARunWithAShareLinkAndAPdf(): void
    {
        $this->queueJsonResponse(StubResponses::createdReportRun(), 201);

        $created = $this->makeClient()->reportRuns()->create(self::ACCOUNT, self::DEFINITION, CreateReportRunRequestDTO::fromArray([
            'grain' => Grain::MONTH,
            'periodKey' => '2026-09',
            'withShareLink' => true,
            'withPdf' => true,
        ]), self::ACTOR_ID);

        $this->assertLastRequest('POST', self::RUNS_PATH);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));
        $this->assertSame([
            'grain' => 'month',
            'period_key' => '2026-09',
            'is_anonymised' => false,
            'share_link' => true,
            'pdf' => true,
        ], $this->getLastRequestJson());

        $this->assertSame('https://reports.example.test/r/stub-share-token', $created->getShareUrl());
        $run = $created->getRun();
        $this->assertSame(self::RUN, $run->getId());
        $this->assertSame(self::DEFINITION, $run->getDefinitionId());
        $this->assertSame(ScopeType::ACCOUNT, $run->getScope()->getScopeType());
        $this->assertSame('2026-09', $run->getPeriod()->getKey());
        $this->assertSame(ReportRunStatus::PDF_PENDING, $run->getStatus());
        $this->assertFalse($run->isPdfReady());
        $this->assertTrue($run->hasShareLink());
        $this->assertNull($run->getCompare());
        $this->assertFalse($run->isAnonymised());
        $this->assertSame(StubResponses::CREATED_AT, $run->getCreatedAt());
        $this->assertSame(StubResponses::CREATED_AT + 90 * 86400, $run->getExpiresAt());
    }

    public function testListsAndShowsRuns(): void
    {
        $readyRun = StubResponses::reportRun(self::RUN, 'pdf_ready');
        $readyRun['compare'] = 'previous_period';
        $this->queueJsonResponse(['data' => [StubResponses::reportRun()]]);
        $this->queueJsonResponse(['data' => $readyRun]);

        $runs = $this->makeClient()->reportRuns()->list(self::ACCOUNT, self::DEFINITION);
        $this->assertLastRequest('GET', self::RUNS_PATH);
        $this->assertCount(1, $runs);

        $run = $this->makeClient()->reportRuns()->get(self::ACCOUNT, self::DEFINITION, self::RUN);
        $this->assertLastRequest('GET', self::RUNS_PATH.'/'.self::RUN);
        $this->assertTrue($run->isPdfReady());
        $this->assertSame(CompareMode::PREVIOUS_PERIOD, $run->getCompare());
    }

    public function testDownloadsThePdfBytes(): void
    {
        $this->queueRawResponse(StubResponses::PDF_BYTES, 200, 'application/pdf');

        $pdf = $this->makeClient()->reportRuns()->downloadPdf(self::ACCOUNT, self::DEFINITION, self::RUN);

        $this->assertLastRequest('GET', self::RUNS_PATH.'/'.self::RUN.'/pdf');
        $this->assertSame(StubResponses::PDF_BYTES, $pdf);
    }

    public function testAPdfThatIsNotReadyIsNotFound(): void
    {
        $this->queueJsonResponse(['message' => 'Not found.'], 404);

        $this->expectException(ReportsNotFoundException::class);

        $this->makeClient()->reportRuns()->downloadPdf(self::ACCOUNT, self::DEFINITION, self::RUN);
    }

    public function testCreatesAShareLink(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::shareLink()], 201);

        $shareLink = $this->makeClient()->reportRuns()->createShareLink(self::ACCOUNT, self::DEFINITION, ReportPeriodRequestDTO::fromArray([
            'grain' => Grain::ISO_WEEK,
            'periodKey' => '2026-W41',
            'isAnonymised' => true,
            'compare' => CompareMode::NONE,
        ]));

        $this->assertLastRequest('POST', '/api/v1/accounts/'.self::ACCOUNT.'/report-definitions/'.self::DEFINITION.'/share-links');
        $this->assertSame(['grain' => 'iso_week', 'period_key' => '2026-W41', 'is_anonymised' => true, 'compare' => 'none'], $this->getLastRequestJson());
        $this->assertSame(self::RUN, $shareLink->getRunId());
        $this->assertSame('https://reports.example.test/r/stub-share-token', $shareLink->getUrl());
        $this->assertSame(StubResponses::CREATED_AT + 90 * 86400, $shareLink->getExpiresAt());
    }
}
