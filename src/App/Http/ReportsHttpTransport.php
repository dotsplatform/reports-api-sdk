<?php

declare(strict_types=1);

/**
 * Description of ReportsHttpTransport.php
 *
 * The only place that talks HTTP to the reports service. Every request carries X-Internal-Gateway-Token
 * (config reports-server.token) and, for write calls, the optional X-Actor-Id. Error statuses become typed
 * exceptions (ErrorResponseMapper); a missing response becomes ReportsConnectionException.
 *
 * The Guzzle handler stack can be passed in, so tests run against a MockHandler.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Http;

use Dotsplatform\ReportsApi\Exceptions\ReportsConfigurationException;
use Dotsplatform\ReportsApi\Exceptions\ReportsConnectionException;
use Dotsplatform\ReportsApi\Exceptions\ReportsUnexpectedResponseException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class ReportsHttpTransport
{
    private const string METHOD_GET = 'GET';

    private const string METHOD_POST = 'POST';

    private const string METHOD_PUT = 'PUT';

    private const string METHOD_PATCH = 'PATCH';

    private const string METHOD_DELETE = 'DELETE';

    private const int FIRST_ERROR_STATUS = 400;

    private ?GuzzleClient $guzzleClient = null;

    public function __construct(
        private readonly string $host,
        private readonly string $token,
        private readonly float $timeoutSeconds = 30.0,
        private readonly ?HandlerStack $handlerStack = null,
    ) {
    }

    /**
     * Builds a path from a template and its parameters; every parameter is URL-encoded.
     */
    public function buildPath(string $template, string ...$parameters): string
    {
        $encodedParameters = [];
        foreach ($parameters as $parameter) {
            $encodedParameters[] = rawurlencode($parameter);
        }

        return vsprintf($template, $encodedParameters);
    }

    /**
     * @param  array<string, scalar|null>  $query
     * @return array<array-key, mixed>
     */
    public function getJson(string $path, array $query = []): array
    {
        $response = $this->send(self::METHOD_GET, $path, [RequestOptions::QUERY => $query], null);

        return $this->decodeJson($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<array-key, mixed>
     */
    public function postJson(string $path, array $body = [], ?string $actorId = null): array
    {
        $response = $this->send(self::METHOD_POST, $path, [RequestOptions::JSON => (object) $body], $actorId);

        return $this->decodeJson($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<array-key, mixed>
     */
    public function putJson(string $path, array $body, ?string $actorId = null): array
    {
        $response = $this->send(self::METHOD_PUT, $path, [RequestOptions::JSON => (object) $body], $actorId);

        return $this->decodeJson($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<array-key, mixed>
     */
    public function patchJson(string $path, array $body, ?string $actorId = null): array
    {
        $response = $this->send(self::METHOD_PATCH, $path, [RequestOptions::JSON => (object) $body], $actorId);

        return $this->decodeJson($response);
    }

    public function delete(string $path, ?string $actorId = null): void
    {
        $this->send(self::METHOD_DELETE, $path, [], $actorId);
    }

    /**
     * The raw response body, for file downloads (PDF).
     */
    public function getBinary(string $path): string
    {
        $response = $this->send(self::METHOD_GET, $path, [], null);

        return (string) $response->getBody();
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function send(string $method, string $path, array $options, ?string $actorId): ResponseInterface
    {
        $options[RequestOptions::HEADERS] = $this->buildHeaders($actorId);

        try {
            $response = $this->getGuzzleClient()->request($method, $path, $options);
        } catch (GuzzleException $exception) {
            throw new ReportsConnectionException('The reports service could not be reached: '.$exception->getMessage(), 0, '', $exception);
        }

        if ($response->getStatusCode() >= self::FIRST_ERROR_STATUS) {
            throw (new ErrorResponseMapper())->map($response);
        }

        return $response;
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(?string $actorId): array
    {
        $headers = [
            'Accept' => 'application/json',
            RequestHeader::INTERNAL_GATEWAY_TOKEN => $this->getConfiguredToken(),
        ];

        if ($actorId !== null && $actorId !== '') {
            $headers[RequestHeader::ACTOR_ID] = $actorId;
        }

        return $headers;
    }

    private function getGuzzleClient(): GuzzleClient
    {
        if ($this->guzzleClient !== null) {
            return $this->guzzleClient;
        }

        $options = [
            'base_uri' => $this->getBaseUri(),
            RequestOptions::TIMEOUT => $this->timeoutSeconds,
            // Error statuses are mapped to typed exceptions in send().
            RequestOptions::HTTP_ERRORS => false,
        ];

        if ($this->handlerStack !== null) {
            $options['handler'] = $this->handlerStack;
        }

        $this->guzzleClient = new GuzzleClient($options);

        return $this->guzzleClient;
    }

    /**
     * Paths are relative ("api/v1/…"), so a host with a path prefix keeps it.
     */
    private function getBaseUri(): string
    {
        if ($this->host === '') {
            throw new ReportsConfigurationException('reports-server.host is empty: set REPORTS_SERVER_HOST.');
        }

        return rtrim($this->host, '/').'/';
    }

    private function getConfiguredToken(): string
    {
        if ($this->token === '') {
            throw new ReportsConfigurationException('reports-server.token is empty: set INTERNAL_GATEWAY_TOKEN.');
        }

        return $this->token;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function decodeJson(ResponseInterface $response): array
    {
        $responseBody = (string) $response->getBody();
        if ($responseBody === '') {
            return [];
        }

        $data = json_decode($responseBody, true);
        if (! is_array($data)) {
            throw new ReportsUnexpectedResponseException('The reports service answered with a body that is not a JSON object.', $response->getStatusCode(), $responseBody);
        }

        return $data;
    }
}
