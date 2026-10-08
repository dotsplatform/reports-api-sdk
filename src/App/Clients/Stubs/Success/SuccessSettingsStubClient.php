<?php

declare(strict_types=1);

/**
 * Description of SuccessSettingsStubClient.php
 *
 * Answers every settings call with StubResponses data, no HTTP.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Stubs\Success;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\Contracts\SettingsClient;
use Dotsplatform\ReportsApi\DTO\Settings\ReportSettingsDTO;
use Dotsplatform\ReportsApi\DTO\Settings\Requests\UpdateReportSettingsDTO;

class SuccessSettingsStubClient implements SettingsClient
{
    public function get(string $accountId): ReportSettingsDTO
    {
        return ReportSettingsDTO::fromResponse(StubResponses::reportSettings());
    }

    public function update(string $accountId, UpdateReportSettingsDTO $settings, ?string $actorId = null): ReportSettingsDTO
    {
        return ReportSettingsDTO::fromResponse(StubResponses::reportSettings());
    }
}
