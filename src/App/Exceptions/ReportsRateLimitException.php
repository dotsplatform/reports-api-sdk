<?php

declare(strict_types=1);

/**
 * Description of ReportsRateLimitException.php
 *
 * 429: the caller sent too many requests. Retry-After (seconds) is kept when the service sends it.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Exceptions;

class ReportsRateLimitException extends ReportsApiException
{
    public function __construct(
        string $message,
        private readonly ?int $retryAfterSeconds,
        string $responseBody = '',
    ) {
        parent::__construct($message, 429, $responseBody);
    }

    public function getRetryAfterSeconds(): ?int
    {
        return $this->retryAfterSeconds;
    }
}
