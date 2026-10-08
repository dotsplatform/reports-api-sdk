<?php

declare(strict_types=1);

/**
 * Description of ReportsHttpTimeouts.php
 *
 * Guzzle timeouts of the reports client, in seconds (config reports-server.connect_timeout / timeout / pdf_timeout).
 * A JSON call should answer fast, so its timeout is short; the PDF download streams a file and gets longer.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Http;

final class ReportsHttpTimeouts
{
    public const float DEFAULT_CONNECT_SECONDS = 2.0;

    public const float DEFAULT_REQUEST_SECONDS = 8.0;

    public const float DEFAULT_PDF_SECONDS = 30.0;

    public function __construct(
        private readonly float $connectSeconds = self::DEFAULT_CONNECT_SECONDS,
        private readonly float $requestSeconds = self::DEFAULT_REQUEST_SECONDS,
        private readonly float $pdfSeconds = self::DEFAULT_PDF_SECONDS,
    ) {
    }

    /**
     * Time to open the TCP / TLS connection.
     */
    public function getConnectSeconds(): float
    {
        return $this->connectSeconds;
    }

    /**
     * Total time of a JSON request.
     */
    public function getRequestSeconds(): float
    {
        return $this->requestSeconds;
    }

    /**
     * Total time of a PDF download.
     */
    public function getPdfSeconds(): float
    {
        return $this->pdfSeconds;
    }
}
