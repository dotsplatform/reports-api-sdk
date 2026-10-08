<?php

declare(strict_types=1);

/*
 * Published as config/reports-server.php in the consumer. The token is the shared secret of every Dots service
 * (X-Internal-Gateway-Token); the reports service answers 401 without it.
 */

return [
    'host' => env('REPORTS_SERVER_HOST', ''),
    'token' => env('INTERNAL_GATEWAY_TOKEN', ''),
    'timeout_seconds' => 30,
];
