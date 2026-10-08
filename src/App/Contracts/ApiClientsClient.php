<?php

declare(strict_types=1);

/**
 * Description of ApiClientsClient.php
 *
 * api-clients of an account (internal token only): list, issue (the plain token is returned once), revoke.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Contracts;

use Dotsplatform\ReportsApi\DTO\ApiClients\ApiClientDTO;
use Dotsplatform\ReportsApi\DTO\ApiClients\IssuedApiClientDTO;
use Dotsplatform\ReportsApi\DTO\ApiClients\Requests\CreateApiClientRequestDTO;

interface ApiClientsClient
{
    /**
     * @return list<ApiClientDTO>
     */
    public function list(string $accountId): array;

    public function create(string $accountId, CreateApiClientRequestDTO $request, ?string $actorId = null): IssuedApiClientDTO;

    /**
     * The token stops working right away. Another account client answers 404.
     */
    public function revoke(string $accountId, string $apiClientId, ?string $actorId = null): void;
}
