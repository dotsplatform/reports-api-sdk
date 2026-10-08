<?php

declare(strict_types=1);

/**
 * Description of IssuedApiClientDTO.php
 *
 * The response of POST api-clients: the client and its plain token. The token is shown only once: hand it to the
 * user right away and do not store it.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\ApiClients;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class IssuedApiClientDTO extends DTO
{
    protected ApiClientDTO $apiClient;

    protected string $plainToken;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'apiClient' => ApiClientDTO::fromResponse(ResponseField::array($data, 'data')),
            'plainToken' => ResponseField::string($data, 'token'),
        ]);
    }

    public function getApiClient(): ApiClientDTO
    {
        return $this->apiClient;
    }

    public function getPlainToken(): string
    {
        return $this->plainToken;
    }
}
