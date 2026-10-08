<?php

declare(strict_types=1);

/**
 * Description of ErrorMappingTest.php
 *
 * Every error status becomes its typed exception: 404, 422 with the field errors, 401, 403, 429 with Retry-After,
 * and the rest as ReportsServerException. Bodies are the standard Laravel JSON shapes the service answers with.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Http;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Exceptions\ReportsApiException;
use Dotsplatform\ReportsApi\Exceptions\ReportsForbiddenException;
use Dotsplatform\ReportsApi\Exceptions\ReportsNotFoundException;
use Dotsplatform\ReportsApi\Exceptions\ReportsRateLimitException;
use Dotsplatform\ReportsApi\Exceptions\ReportsServerException;
use Dotsplatform\ReportsApi\Exceptions\ReportsUnauthorizedException;
use Dotsplatform\ReportsApi\Exceptions\ReportsUnexpectedResponseException;
use Dotsplatform\ReportsApi\Exceptions\ReportsValidationException;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ErrorMappingTest extends HttpClientTestCase
{
    /**
     * @return iterable<string, array{int, class-string<ReportsApiException>, string}>
     */
    public static function errorStatuses(): iterable
    {
        yield 'unauthorized' => [401, ReportsUnauthorizedException::class, 'Unauthorized.'];
        yield 'forbidden' => [403, ReportsForbiddenException::class, 'The token lacks the metrics:read ability'];
        yield 'not found' => [404, ReportsNotFoundException::class, 'Not found.'];
        yield 'server error' => [500, ReportsServerException::class, 'Server Error'];
        yield 'bad gateway' => [502, ReportsServerException::class, 'Bad Gateway'];
    }

    /**
     * @param  class-string<ReportsApiException>  $exceptionClass
     */
    #[DataProvider('errorStatuses')]
    public function testAnErrorStatusBecomesItsTypedException(int $statusCode, string $exceptionClass, string $message): void
    {
        $this->queueJsonResponse(['message' => $message], $statusCode);

        $exception = $this->catchException(fn () => $this->makeClient()->reportDefinitions()->get(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID));

        $this->assertInstanceOf($exceptionClass, $exception);
        $this->assertSame($statusCode, $exception->getStatusCode());
        $this->assertSame($message, $exception->getMessage());
        $this->assertStringContainsString($message, $exception->getResponseBody());
    }

    public function testValidationErrorsAreKeptByField(): void
    {
        $this->queueJsonResponse([
            'message' => 'The metrics field is required. (and 1 more error)',
            'errors' => [
                'metrics' => ['The metrics field is required.'],
                'scope.type' => ['The selected scope.type is invalid.'],
            ],
        ], 422);

        $exception = $this->catchException(fn () => $this->makeClient()->reportDefinitions()->list(StubResponses::ACCOUNT_ID));

        $this->assertInstanceOf(ReportsValidationException::class, $exception);
        $this->assertSame(422, $exception->getStatusCode());
        $this->assertSame(['The metrics field is required.'], $exception->getErrorsFor('metrics'));
        $this->assertSame(['The selected scope.type is invalid.'], $exception->getErrorsFor('scope.type'));
        $this->assertSame([], $exception->getErrorsFor('period'));
        $this->assertCount(2, $exception->getErrors());
    }

    public function testALayoutErrorListIsKept(): void
    {
        $this->queueJsonResponse([
            'message' => 'The layout is invalid.',
            'errors' => ['layout' => ['block 1: unknown type "no_such_block"', 'block 2 (top_table): "limit" must be a whole number']],
        ], 422);

        $exception = $this->catchException(fn () => $this->makeClient()->reportDefinitions()->list(StubResponses::ACCOUNT_ID));

        $this->assertInstanceOf(ReportsValidationException::class, $exception);
        $this->assertSame('block 1: unknown type "no_such_block"', $exception->getErrorsFor('layout')[0] ?? null);
    }

    public function testTooManyRequestsKeepsRetryAfter(): void
    {
        $this->queueJsonResponse(['message' => 'Too Many Attempts.'], 429, ['Retry-After' => '42']);

        $exception = $this->catchException(fn () => $this->makeClient()->metrics()->listDimensions(StubResponses::ACCOUNT_ID));

        $this->assertInstanceOf(ReportsRateLimitException::class, $exception);
        $this->assertSame(429, $exception->getStatusCode());
        $this->assertSame(42, $exception->getRetryAfterSeconds());
    }

    public function testTooManyRequestsWithoutRetryAfter(): void
    {
        $this->queueJsonResponse(['message' => 'Too Many Attempts.'], 429);

        $exception = $this->catchException(fn () => $this->makeClient()->metrics()->listDimensions(StubResponses::ACCOUNT_ID));

        $this->assertInstanceOf(ReportsRateLimitException::class, $exception);
        $this->assertNull($exception->getRetryAfterSeconds());
    }

    public function testAnErrorWithoutAJsonBodyStillHasAMessage(): void
    {
        $this->queueRawResponse('<html>Bad gateway</html>', 502, 'text/html');

        $exception = $this->catchException(fn () => $this->makeClient()->operations()->listTemplates());

        $this->assertInstanceOf(ReportsServerException::class, $exception);
        $this->assertSame('The reports service answered with HTTP 502.', $exception->getMessage());
    }

    public function testAFieldOfAnotherTypeIsAnUnexpectedResponse(): void
    {
        $definition = StubResponses::reportDefinition();
        $definition['trend_periods'] = 'six';
        $this->queueJsonResponse(['data' => $definition]);

        $this->expectException(ReportsUnexpectedResponseException::class);
        $this->expectExceptionMessage('"trend_periods"');

        $this->makeClient()->reportDefinitions()->get(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID);
    }

    public function testAnUnknownEnumValueIsAnUnexpectedResponse(): void
    {
        $definition = StubResponses::reportDefinition();
        $definition['purpose'] = 'billboard';
        $this->queueJsonResponse(['data' => $definition]);

        $this->expectException(ReportsUnexpectedResponseException::class);
        $this->expectExceptionMessage('unknown value "billboard"');

        $this->makeClient()->reportDefinitions()->get(StubResponses::ACCOUNT_ID, StubResponses::DEFINITION_ID);
    }

    /**
     * @param  callable(): mixed  $call
     */
    private function catchException(callable $call): ReportsApiException
    {
        try {
            $call();
        } catch (ReportsApiException $exception) {
            return $exception;
        }

        self::fail('No ReportsApiException was thrown.');
    }
}
