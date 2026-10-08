<?php

declare(strict_types=1);

/*
 * Published as config/reports-server.php in the consumer. The token is the shared secret of every Dots service
 * (X-Internal-Gateway-Token); the reports service answers 401 without it.
 */

return [
    'host' => env('REPORTS_SERVER_HOST', ''),
    'token' => env('INTERNAL_GATEWAY_TOKEN', ''),
    // Seconds. A JSON call answers fast; the PDF download streams a file.
    'connect_timeout' => 2,
    'timeout' => 8,
    'pdf_timeout' => 30,
];
