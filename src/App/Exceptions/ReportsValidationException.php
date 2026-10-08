<?php

declare(strict_types=1);

/**
 * Description of ReportsValidationException.php
 *
 * 422: the request did not pass validation. The errors are keyed by field, Laravel style:
 * ["metrics" => ["The metrics field is required."]].
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Exceptions;

class ReportsValidationException extends ReportsApiException
{
    /**
     * @param  array<string, list<string>>  $errors
     */
    public function __construct(
        string $message,
        private readonly array $errors,
        string $responseBody = '',
    ) {
        parent::__construct($message, 422, $responseBody);
    }

    /**
     * @return array<string, list<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return list<string>
     */
    public function getErrorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
    }
}
