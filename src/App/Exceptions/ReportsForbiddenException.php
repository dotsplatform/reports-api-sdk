<?php

declare(strict_types=1);

/**
 * Description of ReportsForbiddenException.php
 *
 * 403: the caller is known but lacks the ability for this endpoint (account API tokens only).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Exceptions;

class ReportsForbiddenException extends ReportsApiException
{
}
