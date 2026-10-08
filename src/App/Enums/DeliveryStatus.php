<?php

declare(strict_types=1);

/**
 * Description of DeliveryStatus.php
 *
 * The state of one delivery to one recipient (task §10.2, §10.5).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum DeliveryStatus: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case BOUNCED = 'bounced';
    case COMPLAINED = 'complained';
    case REJECTED = 'rejected';
    case FAILED = 'failed';
    case SKIPPED_SUPPRESSED = 'skipped_suppressed';
}
