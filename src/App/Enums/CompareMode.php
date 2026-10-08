<?php

declare(strict_types=1);

/**
 * Description of CompareMode.php
 *
 * What a period is compared with.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum CompareMode: string
{
    case NONE = 'none';
    case PREVIOUS_PERIOD = 'previous_period';
    case SAME_PERIOD_LAST_YEAR = 'same_period_last_year';
}
