<?php

declare(strict_types=1);

/**
 * Description of SubscriptionClearableField.php
 *
 * Subscription fields an update can clear (SubscriptionFormDTO::clear()): they are sent as JSON null, which the
 * service reads as "remove the value".
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum SubscriptionClearableField: string
{
    /** Back to the account report locale. */
    case LOCALE = 'locale';
    /** Back to the whole account. */
    case SCOPE_ID = 'scope_id';
    case SEND_WEEKDAY = 'send_weekday';
    case SEND_MONTH_DAY = 'send_month_day';
}
