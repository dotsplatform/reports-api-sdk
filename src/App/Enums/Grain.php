<?php

declare(strict_types=1);

/**
 * Description of Grain.php
 *
 * A stored period grain (task §7.4).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum Grain: string
{
    case DAY = 'day';
    case ISO_WEEK = 'iso_week';
    case MONTH = 'month';
    case YEAR = 'year';
    case ALL_TIME = 'all_time';
}
