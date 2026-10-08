<?php

declare(strict_types=1);

/**
 * Description of SuccessApiClientsStubClient.php
 *
 * Answers every api-clients call with StubResponses data, no HTTP.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Success;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Contracts\ApiClientsClient;
use Dotsplatform\ReportsApi\DTO\ApiClients\ApiClientDTO;
use Dotsplatform\ReportsApi\DTO\ApiClients\IssuedApiClientDTO;
use Dotsplatform\ReportsApi\DTO\ApiClients\Requests\CreateApiClientRequestDTO;

class SuccessApiClientsStubClient implements ApiClientsClient
{
    public function list(string $accountId): array
    {
        return [ApiClientDTO::fromResponse(StubResponses::apiClient())];
    }

    public function create(string $accountId, CreateApiClientRequestDTO $request, ?string $actorId = null): IssuedApiClientDTO
    {
        return IssuedApiClientDTO::fromResponse(StubResponses::issuedApiClient($request->getName()));
    }

    public function revoke(string $accountId, string $apiClientId, ?string $actorId = null): void
    {
    }
}
