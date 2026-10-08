<?php

declare(strict_types=1);

/**
 * Description of SubscriptionScheduleDTO.php
 *
 * When a subscription is sent: a local time, plus the weekday (iso_week grain, 1 = Monday) or the day of the
 * month (month grain, 1-28).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class SubscriptionScheduleDTO extends DTO
{
    protected string $sendTime;

    protected ?int $sendWeekday = null;

    protected ?int $sendMonthDay = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'sendTime' => ResponseField::string($data, 'send_time'),
            'sendWeekday' => ResponseField::nullableInt($data, 'send_weekday'),
            'sendMonthDay' => ResponseField::nullableInt($data, 'send_month_day'),
        ]);
    }

    /**
     * Local time "HH:MM" in the account timezone.
     */
    public function getSendTime(): string
    {
        return $this->sendTime;
    }

    public function getSendWeekday(): ?int
    {
        return $this->sendWeekday;
    }

    public function getSendMonthDay(): ?int
    {
        return $this->sendMonthDay;
    }
}
