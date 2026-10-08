<?php

declare(strict_types=1);

/**
 * Description of CurrencyPosition.php
 *
 * Where the currency sign goes when a value is formatted.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum CurrencyPosition: string
{
    case PREFIX = 'prefix';
    case SUFFIX = 'suffix';
}
