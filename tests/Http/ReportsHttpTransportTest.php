<?php

declare(strict_types=1);

/**
 * Description of ReportsHttpTransportTest.php
 *
 * The transport: the internal gateway token on every method, X-Actor-Id only when given, JSON bodies, a host with
 * a path prefix, and configuration and connection failures.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Http;

use Dotsplatform\ReportsApi\Exceptions\ReportsConfigurationException;
use Dotsplatform\ReportsApi\Exceptions\ReportsConnectionException;
use Dotsplatform\ReportsApi\Exceptions\ReportsUnexpectedResponseException;
use Dotsplatform\ReportsApi\Http\RequestHeader;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;

class ReportsHttpTransportTest extends HttpClientTestCase
{
    public function testEveryHttpMethodSendsTheInternalGatewayToken(): void
    {
        $transport = $this->makeTransport();
        $this->queueJsonResponse(['data' => []]);
        $this->queueJsonResponse(['data' => []]);
        $this->queueJsonResponse(['data' => []]);
        $this->queueJsonResponse(['data' => []]);
        $this->queueNoContentResponse();
        $this->queueRawResponse('%PDF', 200, 'application/pdf');

        $transport->getJson('api/v1/a');
        $transport->postJson('api/v1/b', ['x' => 1]);
        $transport->putJson('api/v1/c', ['x' => 1]);
        $transport->patchJson('api/v1/d', ['x' => 1]);
        $transport->delete('api/v1/e');
        $transport->getPdf('api/v1/f');

        $methods = [];
        foreach ($this->getRecordedRequests() as $request) {
            $methods[] = $request->getMethod();
            $this->assertSame(self::TOKEN, $request->getHeaderLine(RequestHeader::INTERNAL_GATEWAY_TOKEN));
        }

        $this->assertSame(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'GET'], $methods);
    }

    public function testTheActorIdIsSentOnlyWhenGiven(): void
    {
        $transport = $this->makeTransport();
        $this->queueJsonResponse(['data' => []]);
        $this->queueJsonResponse(['data' => []]);

        $transport->postJson('api/v1/with-actor', [], self::ACTOR_ID);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));

        $transport->postJson('api/v1/without-actor');
        $this->assertFalse($this->getLastRequest()->hasHeader(RequestHeader::ACTOR_ID));
    }

    public function testAnEmptyBodyIsSentAsAJsonObject(): void
    {
        $this->queueJsonResponse(['data' => []]);

        $this->makeTransport()->postJson('api/v1/reset');

        $this->assertSame('{}', $this->getLastRequestRawBody());
        $this->assertSame('application/json', $this->getLastRequest()->getHeaderLine('Content-Type'));
    }

    public function testAHostWithAPathPrefixKeepsIt(): void
    {
        $this->queueJsonResponse(['data' => []]);

        $this->makeTransport('https://gateway.example.test/reports')->getJson('api/v1/templates');

        $this->assertSame('/reports/api/v1/templates', $this->getLastRequest()->getUri()->getPath());
        $this->assertSame('gateway.example.test', $this->getLastRequest()->getUri()->getHost());
    }

    public function testPathParametersAreEncoded(): void
    {
        $path = $this->makeTransport()->buildPath('api/v1/accounts/%s/snapshots/%s/%s', 'account', 'month', '2026/09');

        $this->assertSame('api/v1/accounts/account/snapshots/month/2026%2F09', $path);
    }

    public function testAMissingTokenFailsBeforeAnyRequest(): void
    {
        $this->expectException(ReportsConfigurationException::class);
        $this->expectExceptionMessage('INTERNAL_GATEWAY_TOKEN');

        try {
            $this->makeTransport(self::HOST, '')->getJson('api/v1/templates');
        } finally {
            $this->assertSame([], $this->getRecordedRequests());
        }
    }

    public function testAMissingHostFailsBeforeAnyRequest(): void
    {
        $this->expectException(ReportsConfigurationException::class);
        $this->expectExceptionMessage('REPORTS_SERVER_HOST');

        $this->makeTransport('')->getJson('api/v1/templates');
    }

    public function testNoResponseIsAConnectionError(): void
    {
        $this->queueThrowable(new ConnectException('Connection refused', new Request('GET', 'api/v1/templates')));

        $this->expectException(ReportsConnectionException::class);
        $this->expectExceptionMessage('Connection refused');

        $this->makeTransport()->getJson('api/v1/templates');
    }

    public function testABodyThatIsNotJsonIsUnexpected(): void
    {
        $this->queueRawResponse('<html>gateway</html>', 200, 'text/html');

        $this->expectException(ReportsUnexpectedResponseException::class);

        $this->makeTransport()->getJson('api/v1/templates');
    }
}
