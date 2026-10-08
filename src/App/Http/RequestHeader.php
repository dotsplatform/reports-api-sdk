<?php

declare(strict_types=1);

/**
 * Description of RequestHeader.php
 *
 * Headers the reports service reads from internal callers (see livesite
 * ai/2026-09/DEV-18603-microservices-auth-middleware/internal-request-authorization.md).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Http;

final class RequestHeader
{
    public const string INTERNAL_GATEWAY_TOKEN = 'X-Internal-Gateway-Token';

    /**
     * The livesite user behind a write call; the reports service stores it as `created_by` for the audit log.
     */
    public const string ACTOR_ID = 'X-Actor-Id';
}
