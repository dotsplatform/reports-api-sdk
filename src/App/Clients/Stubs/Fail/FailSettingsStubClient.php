<?php

declare(strict_types=1);

/**
 * Description of FailSettingsStubClient.php
 *
 * Every settings call throws ReportsServerException (503).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Fail;

use Dotsplatform\ReportsApi\Contracts\SettingsClient;
use Dotsplatform\ReportsApi\DTO\Settings\ReportSettingsDTO;
use Dotsplatform\ReportsApi\DTO\Settings\Requests\UpdateReportSettingsDTO;

class FailSettingsStubClient implements SettingsClient
{
    public function get(string $accountId): ReportSettingsDTO
    {
        throw StubFailure::make();
    }

    public function update(string $accountId, UpdateReportSettingsDTO $settings, ?string $actorId = null): ReportSettingsDTO
    {
        throw StubFailure::make();
    }
}
