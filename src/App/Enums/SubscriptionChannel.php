<?php

declare(strict_types=1);

/**
 * Description of SubscriptionChannel.php
 *
 * How a subscription is delivered (task §10.1). Telegram follows in Phase 6.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum SubscriptionChannel: string
{
    case EMAIL = 'email';
}
