<?php

declare(strict_types=1);

/**
 * Description of ReportRunStatus.php
 *
 * The state of a report run and its PDF.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum ReportRunStatus: string
{
    case READY = 'ready';
    case PDF_PENDING = 'pdf_pending';
    case PDF_READY = 'pdf_ready';
    case PDF_FAILED = 'pdf_failed';
}
