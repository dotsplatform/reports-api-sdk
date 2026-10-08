<?php

declare(strict_types=1);

/**
 * Description of SettingsClient.php
 *
 * GET / PUT settings (task §8.1). Changing the value bands queues a re-aggregation on the service.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Contracts;

use Dotsplatform\ReportsApi\DTO\Settings\ReportSettingsDTO;
use Dotsplatform\ReportsApi\DTO\Settings\Requests\UpdateReportSettingsDTO;

interface SettingsClient
{
    public function get(string $accountId): ReportSettingsDTO;

    /**
     * Replaces the stored overrides and returns the effective settings.
     */
    public function update(string $accountId, UpdateReportSettingsDTO $settings, ?string $actorId = null): ReportSettingsDTO;
}
