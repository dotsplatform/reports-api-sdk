<?php

declare(strict_types=1);

/**
 * Description of ScopeType.php
 *
 * The level a metric is read at; the id is always the uuid token of that entity.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum ScopeType: string
{
    case PLATFORM = 'platform';
    case ACCOUNT = 'account';
    case CITY = 'city';
    case COMPANY = 'company';
    case COMPANY_ADDRESS = 'company_address';
}
