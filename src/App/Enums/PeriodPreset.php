<?php

declare(strict_types=1);

/**
 * Description of PeriodPreset.php
 *
 * Period presets of POST metrics/query (task §7.4). MONTH and YEAR need a key, CUSTOM needs from and to.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum PeriodPreset: string
{
    case TODAY = 'today';
    case YESTERDAY = 'yesterday';
    case THIS_WEEK = 'this_week';
    case LAST_WEEK = 'last_week';
    case THIS_MONTH = 'this_month';
    case LAST_MONTH = 'last_month';
    case MONTH = 'month';
    case THIS_YEAR = 'this_year';
    case YEAR = 'year';
    case LAST_30_DAYS = 'last_30_days';
    case LAST_4_WEEKS = 'last_4_weeks';
    case LAST_12_WEEKS = 'last_12_weeks';
    case ALL_TIME = 'all_time';
    case CUSTOM = 'custom';
}
