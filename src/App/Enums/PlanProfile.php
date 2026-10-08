<?php

declare(strict_types=1);

/**
 * Description of PlanProfile.php
 *
 * The plan a report set is built for (task §8.1).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum PlanProfile: string
{
    case PRO = 'pro';
    case STD_PLUS = 'std_plus';
    case MARKETPLACE = 'marketplace';
    case QR = 'qr';
    case CUSTOM = 'custom';
}
