<?php

declare(strict_types=1);

/**
 * Description of ReportsApiException.php
 *
 * The base of every error the SDK throws: a failed HTTP call, an error status or an unexpected response body.
 * The status code is 0 when no response was received.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Exceptions;

use RuntimeException;
use Throwable;

class ReportsApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        private readonly string $responseBody = '',
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
}
