<?php

declare(strict_types=1);

/**
 * Description of ReportPurpose.php
 *
 * What a report definition is for (task §8.1).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum ReportPurpose: string
{
    case OWNER = 'owner';
    case TELEGRAM = 'telegram';
    case EMAIL = 'email';
    case LOCATION = 'location';
    case INTERNAL = 'internal';
    case DASHBOARD = 'dashboard';
}
