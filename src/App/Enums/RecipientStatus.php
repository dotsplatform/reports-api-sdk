<?php

declare(strict_types=1);

/**
 * Description of RecipientStatus.php
 *
 * The state of one subscription recipient.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum RecipientStatus: string
{
    case ACTIVE = 'active';
    case UNSUBSCRIBED = 'unsubscribed';
    case SUPPRESSED = 'suppressed';
}
