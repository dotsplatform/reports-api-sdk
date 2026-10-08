<?php

declare(strict_types=1);

/**
 * Description of ReportDefinitionClearableField.php
 *
 * Report definition fields an update can clear (ReportDefinitionFormDTO::clear()): they are sent as JSON null.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Enums;

enum ReportDefinitionClearableField: string
{
    /** The report follows the whole account again. */
    case SCOPE_ID = 'scope_id';
}
