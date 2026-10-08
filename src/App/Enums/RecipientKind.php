<?php

declare(strict_types=1);

/**
 * Description of RecipientKind.php
 *
 * The address type of one subscription recipient: an email address or a Telegram chat / livesite user.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum RecipientKind: string
{
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
}
