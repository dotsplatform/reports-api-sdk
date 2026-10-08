<?php

declare(strict_types=1);

/**
 * Description of SettingsHttpClient.php
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Clients\Http;

use Dotsplatform\ReportsApi\Contracts\SettingsClient;
use Dotsplatform\ReportsApi\DTO\Settings\ReportSettingsDTO;
use Dotsplatform\ReportsApi\DTO\Settings\Requests\UpdateReportSettingsDTO;
use Dotsplatform\ReportsApi\Http\ReportsHttpTransport;
use Dotsplatform\ReportsApi\Support\ResponseField;

class SettingsHttpClient implements SettingsClient
{
    private const string SETTINGS_URL_TEMPLATE = 'api/v1/accounts/%s/settings';

    public function __construct(
        private readonly ReportsHttpTransport $transport,
    ) {
    }

    public function get(string $accountId): ReportSettingsDTO
    {
        $path = $this->transport->buildPath(self::SETTINGS_URL_TEMPLATE, $accountId);
        $response = $this->transport->getJson($path);

        return ReportSettingsDTO::fromResponse(ResponseField::array($response, 'data'));
    }

    public function update(string $accountId, UpdateReportSettingsDTO $settings, ?string $actorId = null): ReportSettingsDTO
    {
        $path = $this->transport->buildPath(self::SETTINGS_URL_TEMPLATE, $accountId);
        $response = $this->transport->putJson($path, $settings->toRequestBody(), $actorId);

        return ReportSettingsDTO::fromResponse(ResponseField::array($response, 'data'));
    }
}
