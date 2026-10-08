<?php

declare(strict_types=1);

/**
 * Description of FailApiClientsStubClient.php
 *
 * Every api-clients call throws ReportsServerException (503).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Contracts\ApiClientsClient;
use Dotsplatform\ReportsApi\DTO\ApiClients\IssuedApiClientDTO;
use Dotsplatform\ReportsApi\DTO\ApiClients\Requests\CreateApiClientRequestDTO;

class FailApiClientsStubClient implements ApiClientsClient
{
    public function list(string $accountId): array
    {
        throw StubFailure::make();
    }

    public function create(string $accountId, CreateApiClientRequestDTO $request, ?string $actorId = null): IssuedApiClientDTO
    {
        throw StubFailure::make();
    }

    public function revoke(string $accountId, string $apiClientId, ?string $actorId = null): void
    {
        throw StubFailure::make();
    }
}
