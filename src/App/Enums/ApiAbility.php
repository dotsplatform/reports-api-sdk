<?php

declare(strict_types=1);

/**
 * Description of ApiAbility.php
 *
 * Abilities of an account API token (task §11.1).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum ApiAbility: string
{
    case METRICS_READ = 'metrics:read';
    case REPORTS_READ = 'reports:read';
    case REPORTS_WRITE = 'reports:write';
    case SUBSCRIPTIONS_WRITE = 'subscriptions:write';
}
