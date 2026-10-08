<?php

declare(strict_types=1);

/**
 * Description of SettingsHttpClientTest.php
 *
 * settings(): read, and replace with every field sent (null = default).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\Tests\Clients;

use Dotsplatform\ReportsApi\Clients\Stubs\StubResponses;
use Dotsplatform\ReportsApi\DTO\Settings\Requests\UpdateReportSettingsDTO;
use Dotsplatform\ReportsApi\Enums\CurrencyPosition;
use Dotsplatform\ReportsApi\Enums\PlanProfile;
use Dotsplatform\ReportsApi\Enums\ReportLocale;
use Dotsplatform\ReportsApi\Http\RequestHeader;
use Dotsplatform\ReportsApi\Tests\HttpClientTestCase;

class SettingsHttpClientTest extends HttpClientTestCase
{
    private const string ACCOUNT = StubResponses::ACCOUNT_ID;

    private const string SETTINGS_PATH = '/api/v1/accounts/'.self::ACCOUNT.'/settings';

    public function testReadsTheSettings(): void
    {
        $settingsData = StubResponses::reportSettings();
        $settingsData['tariff_zone_target_minutes'] = ['0199a000-0000-7000-8000-0000000b0001' => 45];
        $this->queueJsonResponse(['data' => $settingsData]);

        $settings = $this->makeClient()->settings()->get(self::ACCOUNT);

        $this->assertLastRequest('GET', self::SETTINGS_PATH);
        $this->assertSame('ua', $settings->getLocale());
        $this->assertSame('Europe/Kyiv', $settings->getTimezone());
        $this->assertSame('UAH', $settings->getCurrency());
        $this->assertSame('₴', $settings->getCurrencySign());
        $this->assertSame(CurrencyPosition::SUFFIX, $settings->getCurrencyPosition());
        $this->assertSame(' ', $settings->getThousandsSeparator());
        $this->assertSame(',', $settings->getDecimalSeparator());
        $this->assertSame([20000, 50000], $settings->getValueBandsCents());
        $this->assertSame([30, 40, 50, 60, 70, 80], $settings->getDistanceBandTargetMinutes());
        $this->assertSame(['0199a000-0000-7000-8000-0000000b0001' => 45], $settings->getTariffZoneTargetMinutes());
        $this->assertSame(90, $settings->getOutsideZonesTargetMinutes());
        $this->assertSame([18, 20], $settings->getPeakHours());
        $this->assertSame([], $settings->getHiddenBlocks());
        $this->assertSame(PlanProfile::PRO, $settings->getPlanProfile());
    }

    public function testReplacesTheSettings(): void
    {
        $this->queueJsonResponse(['data' => StubResponses::reportSettings()]);

        $this->makeClient()->settings()->update(self::ACCOUNT, UpdateReportSettingsDTO::fromArray([
            'locale' => ReportLocale::EN,
            'currencyPosition' => CurrencyPosition::PREFIX,
            'valueBandsCents' => [50000, 20000],
            'tariffZoneTargetMinutes' => [],
            'peakHours' => [12, 14],
            'hiddenBlocks' => ['delivery'],
        ]), self::ACTOR_ID);

        $this->assertLastRequest('PUT', self::SETTINGS_PATH);
        $this->assertSame(self::ACTOR_ID, $this->getLastRequest()->getHeaderLine(RequestHeader::ACTOR_ID));
        $this->assertSame([
            'locale' => 'en',
            'currency_sign' => null,
            'currency_position' => 'prefix',
            'thousands_separator' => null,
            'decimal_separator' => null,
            'value_bands_cents' => [50000, 20000],
            'distance_band_target_minutes' => null,
            'tariff_zone_target_minutes' => [],
            'peak_hours' => [12, 14],
            'hidden_blocks' => ['delivery'],
            'plan_profile' => null,
        ], $this->getLastRequestJson());
        $this->assertStringContainsString('"tariff_zone_target_minutes":{}', $this->getLastRequestRawBody());
    }
}
