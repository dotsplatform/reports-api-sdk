<?php

declare(strict_types=1);

/**
 * Description of SubscriptionChannel.php
 *
 * How a subscription is delivered (task §10.1). Telegram goes through the account's notifier bot: the chat must be
 * connected to the bot (the /start flow).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum SubscriptionChannel: string
{
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
}
