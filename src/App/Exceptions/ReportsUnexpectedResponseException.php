<?php

declare(strict_types=1);

/**
 * Description of ReportsUnexpectedResponseException.php
 *
 * The response body does not have the documented shape (a field is missing or has another type).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Exceptions;

class ReportsUnexpectedResponseException extends ReportsApiException
{
}
