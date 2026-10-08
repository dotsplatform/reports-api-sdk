<?php

declare(strict_types=1);

/**
 * Description of StubFailure.php
 *
 * The error every Fail*StubClient method throws: the reports service answered 503.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Exceptions\ReportsServerException;

final class StubFailure
{
    public const int STATUS_CODE = 503;

    public const string MESSAGE = 'The reports service is unavailable (FailReportsStubClient).';

    public static function make(): ReportsServerException
    {
        return new ReportsServerException(self::MESSAGE, self::STATUS_CODE);
    }
}
