<?php

declare(strict_types=1);

/**
 * Description of ReportSettingsDTO.php
 *
 * The effective report settings of an account (task §8.1): the stored overrides merged with the defaults.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Settings;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\CurrencyPosition;
use Dotsplatform\ReportsApi\Enums\PlanProfile;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ReportSettingsDTO extends DTO
{
    protected string $locale;

    protected string $timezone;

    protected string $currency;

    protected string $currencySign;

    protected string $currencyPositionValue;

    protected string $thousandsSeparator;

    protected string $decimalSeparator;

    /** @var list<int> */
    protected array $valueBandsCents = [];

    /** @var list<int> */
    protected array $distanceBandTargetMinutes = [];

    /** @var array<string, int> */
    protected array $tariffZoneTargetMinutes = [];

    protected int $outsideZonesTargetMinutes;

    /** @var list<int> */
    protected array $peakHours = [];

    /** @var list<string> */
    protected array $hiddenBlocks = [];

    protected string $planProfileValue;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'locale' => ResponseField::string($data, 'locale'),
            'timezone' => ResponseField::string($data, 'timezone'),
            'currency' => ResponseField::string($data, 'currency'),
            'currencySign' => ResponseField::string($data, 'currency_sign'),
            'currencyPositionValue' => ResponseField::string($data, 'currency_position'),
            'thousandsSeparator' => ResponseField::string($data, 'thousands_separator'),
            'decimalSeparator' => ResponseField::string($data, 'decimal_separator'),
            'valueBandsCents' => ResponseField::intList($data, 'value_bands_cents'),
            'distanceBandTargetMinutes' => ResponseField::intList($data, 'distance_band_target_minutes'),
            'tariffZoneTargetMinutes' => ResponseField::intMap($data, 'tariff_zone_target_minutes'),
            'outsideZonesTargetMinutes' => ResponseField::int($data, 'outside_zones_target_minutes'),
            'peakHours' => ResponseField::intList($data, 'peak_hours'),
            'hiddenBlocks' => ResponseField::stringList($data, 'hidden_blocks'),
            'planProfileValue' => ResponseField::string($data, 'plan_profile'),
        ]);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * ISO 4217 code.
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCurrencySign(): string
    {
        return $this->currencySign;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getCurrencyPositionValue(): string
    {
        return $this->currencyPositionValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getCurrencyPositionValue()).
     */
    public function getCurrencyPosition(): ?CurrencyPosition
    {
        return CurrencyPosition::tryFrom($this->currencyPositionValue);
    }

    public function getThousandsSeparator(): string
    {
        return $this->thousandsSeparator;
    }

    public function getDecimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    /**
     * Order value band limits, in cents, ascending.
     *
     * @return list<int>
     */
    public function getValueBandsCents(): array
    {
        return $this->valueBandsCents;
    }

    /**
     * @return list<int>
     */
    public function getDistanceBandTargetMinutes(): array
    {
        return $this->distanceBandTargetMinutes;
    }

    /**
     * Tariff zone uuid => target delivery minutes.
     *
     * @return array<string, int>
     */
    public function getTariffZoneTargetMinutes(): array
    {
        return $this->tariffZoneTargetMinutes;
    }

    public function getOutsideZonesTargetMinutes(): int
    {
        return $this->outsideZonesTargetMinutes;
    }

    /**
     * [from hour, to hour].
     *
     * @return list<int>
     */
    public function getPeakHours(): array
    {
        return $this->peakHours;
    }

    /**
     * @return list<string>
     */
    public function getHiddenBlocks(): array
    {
        return $this->hiddenBlocks;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getPlanProfileValue(): string
    {
        return $this->planProfileValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getPlanProfileValue()).
     */
    public function getPlanProfile(): ?PlanProfile
    {
        return PlanProfile::tryFrom($this->planProfileValue);
    }
}
