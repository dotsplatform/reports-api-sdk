<?php

declare(strict_types=1);

/**
 * Description of ApiClientsHttpClient.php
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Http;

use Dotsplatform\ReportsApi\Contracts\ApiClientsClient;
use Dotsplatform\ReportsApi\DTO\ApiClients\ApiClientDTO;
use Dotsplatform\ReportsApi\DTO\ApiClients\IssuedApiClientDTO;
use Dotsplatform\ReportsApi\DTO\ApiClients\Requests\CreateApiClientRequestDTO;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ApiClientsHttpClient implements ApiClientsClient
{
    private const string API_CLIENTS_URL_TEMPLATE = 'api/v1/accounts/%s/api-clients';

    private const string API_CLIENT_URL_TEMPLATE = 'api/v1/accounts/%s/api-clients/%s';

    public function __construct(
        private readonly ReportsHttpTransport $transport,
    ) {
    }

    public function list(string $accountId): array
    {
        $path = $this->transport->buildPath(self::API_CLIENTS_URL_TEMPLATE, $accountId);
        $response = $this->transport->getJson($path);

        $apiClients = [];
        foreach (ResponseField::listOfObjects($response, 'data') as $apiClientData) {
            $apiClients[] = ApiClientDTO::fromResponse($apiClientData);
        }

        return $apiClients;
    }

    public function create(string $accountId, CreateApiClientRequestDTO $request, ?string $actorId = null): IssuedApiClientDTO
    {
        $path = $this->transport->buildPath(self::API_CLIENTS_URL_TEMPLATE, $accountId);
        $response = $this->transport->postJson($path, $request->toRequestBody(), $actorId);

        return IssuedApiClientDTO::fromResponse($response);
    }

    public function revoke(string $accountId, string $apiClientId, ?string $actorId = null): void
    {
        $path = $this->transport->buildPath(self::API_CLIENT_URL_TEMPLATE, $accountId, $apiClientId);
        $this->transport->delete($path, $actorId);
    }
}
