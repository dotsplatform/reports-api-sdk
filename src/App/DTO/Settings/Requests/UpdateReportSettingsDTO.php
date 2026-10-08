<?php

declare(strict_types=1);

/**
 * Description of UpdateReportSettingsDTO.php
 *
 * The body of PUT settings. PUT replaces the stored overrides: every field is sent, and a null field means
 * "use the default". To change one field, start from the current settings.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Settings\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\CurrencyPosition;
use Dotsplatform\ReportsApi\Enums\PlanProfile;
use Dotsplatform\ReportsApi\Enums\ReportLocale;

class UpdateReportSettingsDTO extends DTO
{
    protected ?ReportLocale $locale = null;

    protected ?string $currencySign = null;

    protected ?CurrencyPosition $currencyPosition = null;

    protected ?string $thousandsSeparator = null;

    protected ?string $decimalSeparator = null;

    /** @var list<int>|null */
    protected ?array $valueBandsCents = null;

    /**
     * Exactly 6 values when set.
     *
     * @var list<int>|null
     */
    protected ?array $distanceBandTargetMinutes = null;

    /** @var array<string, int>|null */
    protected ?array $tariffZoneTargetMinutes = null;

    /**
     * [from hour, to hour] when set.
     *
     * @var list<int>|null
     */
    protected ?array $peakHours = null;

    /** @var list<string>|null */
    protected ?array $hiddenBlocks = null;

    protected ?PlanProfile $planProfile = null;

    public function getLocale(): ?ReportLocale
    {
        return $this->locale;
    }

    public function getCurrencySign(): ?string
    {
        return $this->currencySign;
    }

    public function getCurrencyPosition(): ?CurrencyPosition
    {
        return $this->currencyPosition;
    }

    public function getThousandsSeparator(): ?string
    {
        return $this->thousandsSeparator;
    }

    public function getDecimalSeparator(): ?string
    {
        return $this->decimalSeparator;
    }

    /**
     * @return list<int>|null
     */
    public function getValueBandsCents(): ?array
    {
        return $this->valueBandsCents;
    }

    /**
     * @return list<int>|null
     */
    public function getDistanceBandTargetMinutes(): ?array
    {
        return $this->distanceBandTargetMinutes;
    }

    /**
     * @return array<string, int>|null
     */
    public function getTariffZoneTargetMinutes(): ?array
    {
        return $this->tariffZoneTargetMinutes;
    }

    /**
     * @return list<int>|null
     */
    public function getPeakHours(): ?array
    {
        return $this->peakHours;
    }

    /**
     * @return list<string>|null
     */
    public function getHiddenBlocks(): ?array
    {
        return $this->hiddenBlocks;
    }

    public function getPlanProfile(): ?PlanProfile
    {
        return $this->planProfile;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestBody(): array
    {
        $tariffZoneTargetMinutes = null;
        if ($this->tariffZoneTargetMinutes !== null) {
            // An empty map must stay a JSON object.
            $tariffZoneTargetMinutes = (object) $this->tariffZoneTargetMinutes;
        }

        return [
            'locale' => $this->locale?->value,
            'currency_sign' => $this->currencySign,
            'currency_position' => $this->currencyPosition?->value,
            'thousands_separator' => $this->thousandsSeparator,
            'decimal_separator' => $this->decimalSeparator,
            'value_bands_cents' => $this->valueBandsCents,
            'distance_band_target_minutes' => $this->distanceBandTargetMinutes,
            'tariff_zone_target_minutes' => $tariffZoneTargetMinutes,
            'peak_hours' => $this->peakHours,
            'hidden_blocks' => $this->hiddenBlocks,
            'plan_profile' => $this->planProfile?->value,
        ];
    }
}
