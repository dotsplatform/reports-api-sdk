<?php

declare(strict_types=1);

/**
 * Description of SubscriptionFormat.php
 *
 * What a subscription email carries.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum SubscriptionFormat: string
{
    case DIGEST = 'digest';
    case DIGEST_WITH_PDF = 'digest_with_pdf';
    case LINK_ONLY = 'link_only';
}
