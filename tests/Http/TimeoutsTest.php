<?php

declare(strict_types=1);

/**
 * Description of TimeoutsTest.php
 *
 * E-S10: every JSON call has a 2 s connect and an 8 s total timeout, the PDF download 30 s, all configurable; a timeout
 * surfaces as ReportsConnectionException.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Http;

use Dotsplatform\ReportsApi\Clients\ReportsHttpClient;
use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Exceptions\ReportsConnectionException;
use Dotsplatform\ReportsApi\Http\ReportsHttpTimeouts;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class TimeoutsTest extends HttpClientTestCase
{
    /** @var list<array<string, mixed>> */
    private array $capturedOptions = [];

    public function testAJsonCallHasTheDefaultConnectAndRequestTimeouts(): void
    {
        $this->queueHandler($this->makeCapturingHandler(json_encode(['data' => []], JSON_THROW_ON_ERROR)));

        $this->makeClient()->operations()->listTemplates();

        $this->assertSame(2.0, $this->capturedOptions[0]['connect_timeout'] ?? null);
        $this->assertSame(8.0, $this->capturedOptions[0]['timeout'] ?? null);
    }

    public function testThePdfDownloadHasTheLongerTimeout(): void
    {
        $this->queueHandler($this->makeCapturingHandler(StubResponses::PDF_BYTES));

        $pdf = $this->makeClient()->reportRuns()->downloadPdf(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID, StubResponses::RUN_ID);

        $this->assertSame(StubResponses::PDF_BYTES, $pdf);
        $this->assertSame(2.0, $this->capturedOptions[0]['connect_timeout'] ?? null);
        $this->assertSame(30.0, $this->capturedOptions[0]['timeout'] ?? null);
        $this->assertSame('application/pdf, application/json', $this->getLastRequest()->getHeaderLine('Accept'));
    }

    public function testTimeoutsAreConfigurable(): void
    {
        $mockHandler = new MockHandler([
            $this->makeCapturingHandler(json_encode(['data' => []], JSON_THROW_ON_ERROR)),
            $this->makeCapturingHandler(StubResponses::PDF_BYTES),
        ]);
        $transport = new ReportsHttpTransport(self::HOST, self::TOKEN, new ReportsHttpTimeouts(1.5, 4.0, 60.0), HandlerStack::create($mockHandler));
        $reportsClient = new ReportsHttpClient($transport);

        $reportsClient->operations()->listTemplates();
        $reportsClient->reportRuns()->downloadPdf(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID, StubResponses::RUN_ID);

        $this->assertSame(1.5, $this->capturedOptions[0]['connect_timeout'] ?? null);
        $this->assertSame(4.0, $this->capturedOptions[0]['timeout'] ?? null);
        $this->assertSame(60.0, $this->capturedOptions[1]['timeout'] ?? null);
    }

    public function testATimeoutIsAConnectionError(): void
    {
        $this->queueHandler(function (RequestInterface $request): never {
            throw new ConnectException('cURL error 28: Operation timed out after 8001 milliseconds with 0 bytes received', $request);
        });

        $this->expectException(ReportsConnectionException::class);
        $this->expectExceptionMessage('Operation timed out');

        $this->makeClient()->operations()->listTemplates();
    }

    /**
     * @return callable(RequestInterface, array<string, mixed>): Response
     */
    private function makeCapturingHandler(string $body): callable
    {
        return function (RequestInterface $request, array $options) use ($body): Response {
            $this->capturedOptions[] = $options;

            return new Response(200, [], $body);
        };
    }
}
