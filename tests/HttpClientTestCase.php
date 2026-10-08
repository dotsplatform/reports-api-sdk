<?php

declare(strict_types=1);

/**
 * Description of HttpClientTestCase.php
 *
 * Runs the real HTTP client against a Guzzle MockHandler and records every request. After each test it asserts that
 * every request carried X-Internal-Gateway-Token, so the header is checked by every client test.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests;

use ArrayObject;
use Dotsplatform\ReportsApi\Clients\ReportsHttpClient;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Http\RequestHeader;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Throwable;

abstract class HttpClientTestCase extends TestCase
{
    protected const string HOST = 'https://reports.example.test';

    protected const string TOKEN = 'internal-gateway-secret';

    protected const string ACTOR_ID = 'livesite-user-7';

    /**
     * Guzzle history middleware entries: ['request' => RequestInterface, 'response' => …, …].
     *
     * @var ArrayObject<int, array<array-key, mixed>>
     */
    private ArrayObject $history;

    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->history = new ArrayObject();
        $this->mockHandler = new MockHandler();
    }

    protected function assertPostConditions(): void
    {
        foreach ($this->getRecordedRequests() as $request) {
            $this->assertSame(self::TOKEN, $request->getHeaderLine(RequestHeader::INTERNAL_GATEWAY_TOKEN), 'Every request must carry the internal gateway token.');
        }

        parent::assertPostConditions();
    }

    protected function makeClient(): ReportsHttpClient
    {
        return new ReportsHttpClient($this->makeTransport());
    }

    protected function makeTransport(string $host = self::HOST, string $token = self::TOKEN): ReportsHttpTransport
    {
        $handlerStack = HandlerStack::create($this->mockHandler);
        // The middleware appends to the ArrayObject it is given, so the test reads the same object.
        $historyContainer = $this->history;
        $handlerStack->push(Middleware::history($historyContainer));

        return new ReportsHttpTransport($host, $token, 5.0, $handlerStack);
    }

    /**
     * @param  array<array-key, mixed>  $body
     * @param  array<string, string>  $headers
     */
    protected function queueJsonResponse(array $body, int $statusCode = 200, array $headers = []): void
    {
        $headers['Content-Type'] = 'application/json';
        $this->mockHandler->append(new Response($statusCode, $headers, json_encode($body, JSON_THROW_ON_ERROR)));
    }

    protected function queueRawResponse(string $body, int $statusCode = 200, string $contentType = 'application/json'): void
    {
        $this->mockHandler->append(new Response($statusCode, ['Content-Type' => $contentType], $body));
    }

    protected function queueNoContentResponse(): void
    {
        $this->mockHandler->append(new Response(204));
    }

    protected function queueThrowable(Throwable $throwable): void
    {
        $this->mockHandler->append($throwable);
    }

    /**
     * @return list<RequestInterface>
     */
    protected function getRecordedRequests(): array
    {
        $requests = [];
        foreach ($this->history as $transaction) {
            $request = $transaction['request'] ?? null;
            if ($request instanceof RequestInterface) {
                $requests[] = $request;
            }
        }

        return $requests;
    }

    protected function getLastRequest(): RequestInterface
    {
        $requests = $this->getRecordedRequests();
        $this->assertNotSame([], $requests, 'No request was sent.');

        return $requests[array_key_last($requests)];
    }

    protected function assertLastRequest(string $method, string $path): void
    {
        $request = $this->getLastRequest();
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($path, $request->getUri()->getPath());
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function getLastRequestJson(): array
    {
        $body = json_decode((string) $this->getLastRequest()->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($body);

        return $body;
    }

    protected function getLastRequestRawBody(): string
    {
        return (string) $this->getLastRequest()->getBody();
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function getLastRequestQuery(): array
    {
        parse_str($this->getLastRequest()->getUri()->getQuery(), $query);

        return $query;
    }
}
