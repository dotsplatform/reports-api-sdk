<?php

declare(strict_types=1);

/**
 * Description of ReportsUnauthorizedException.php
 *
 * 401: the X-Internal-Gateway-Token header is missing or wrong (config reports-server.token).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Exceptions;

class ReportsUnauthorizedException extends ReportsApiException
{
}
