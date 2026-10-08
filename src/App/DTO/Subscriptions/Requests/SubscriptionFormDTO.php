<?php

declare(strict_types=1);

/**
 * Description of SubscriptionFormDTO.php
 *
 * The body of POST and PATCH subscriptions. A null field is not sent. On create, definitionId and recipients are
 * required; on update, recipients (when set) replace the whole list. At most 20 recipients.
 *
 * The channel (email by default) is chosen on create only: toRequestBody() sends it, toUpdateRequestBody() never does,
 * because the service rejects a channel in PATCH. The recipients must match the channel (see RecipientRequestDTO).
 *
 * To remove a value (locale, scope id, weekday, month day) in an update, call clear(): a field left null is not sent,
 * a cleared field is sent as null.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportLocale;
use Dotsplatform\ReportsApi\Enums\ScopeType;
use Dotsplatform\ReportsApi\Enums\SubscriptionChannel;
use Dotsplatform\ReportsApi\Enums\SubscriptionClearableField;
use Dotsplatform\ReportsApi\Enums\SubscriptionFormat;

class SubscriptionFormDTO extends DTO
{
    protected ?string $definitionId = null;

    protected ?ScopeType $scopeType = null;

    protected ?string $scopeId = null;

    /**
     * DAY, ISO_WEEK or MONTH.
     */
    protected ?Grain $grain = null;

    /**
     * Local "HH:MM".
     */
    protected ?string $sendTime = null;

    /**
     * 1 (Monday) - 7, for the iso_week grain.
     */
    protected ?int $sendWeekday = null;

    /**
     * 1 - 28, for the month grain.
     */
    protected ?int $sendMonthDay = null;

    protected ?SubscriptionFormat $format = null;

    protected ?ReportLocale $locale = null;

    protected ?bool $isActive = null;

    /**
     * Create only; null sends nothing and the service uses email.
     */
    protected ?SubscriptionChannel $channel = null;

    /** @var list<RecipientRequestDTO>|null */
    protected ?array $recipients = null;

    /**
     * Fields sent as JSON null; set them only through clear().
     *
     * @var list<SubscriptionClearableField>
     */
    protected array $clearedFields = [];

    /**
     * A copy that also sends these fields as JSON null, so the service removes their values. A cleared field is
     * sent as null even when a value was set. Fields that are neither set nor cleared are not sent.
     */
    public function clear(SubscriptionClearableField ...$fields): static
    {
        $clearedFields = $this->clearedFields;
        foreach ($fields as $field) {
            if (in_array($field, $clearedFields, true)) {
                continue;
            }

            $clearedFields[] = $field;
        }

        return $this->copy(['clearedFields' => $clearedFields]);
    }

    /**
     * @return list<SubscriptionClearableField>
     */
    public function getClearedFields(): array
    {
        return $this->clearedFields;
    }

    public function getDefinitionId(): ?string
    {
        return $this->definitionId;
    }

    public function getScopeType(): ?ScopeType
    {
        return $this->scopeType;
    }

    public function getScopeId(): ?string
    {
        return $this->scopeId;
    }

    public function getGrain(): ?Grain
    {
        return $this->grain;
    }

    public function getSendTime(): ?string
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

    public function getFormat(): ?SubscriptionFormat
    {
        return $this->format;
    }

    public function getLocale(): ?ReportLocale
    {
        return $this->locale;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function getChannel(): ?SubscriptionChannel
    {
        return $this->channel;
    }

    /**
     * @return list<RecipientRequestDTO>|null
     */
    public function getRecipients(): ?array
    {
        return $this->recipients;
    }

    /**
     * The POST (create) body, with the channel when set.
     *
     * @return array<string, mixed>
     */
    public function toRequestBody(): array
    {
        $body = $this->toUpdateRequestBody();

        if ($this->channel !== null) {
            $body['channel'] = $this->channel->value;
        }

        return $body;
    }

    /**
     * The PATCH (update) body: never the channel, which cannot change after create.
     *
     * @return array<string, mixed>
     */
    public function toUpdateRequestBody(): array
    {
        $body = [
            'definition_id' => $this->definitionId,
            'scope_type' => $this->scopeType?->value,
            'scope_id' => $this->scopeId,
            'grain' => $this->grain?->value,
            'send_time' => $this->sendTime,
            'send_weekday' => $this->sendWeekday,
            'send_month_day' => $this->sendMonthDay,
            'format' => $this->format?->value,
            'locale' => $this->locale?->value,
            'is_active' => $this->isActive,
        ];

        $body = array_filter($body, fn (mixed $value): bool => $value !== null);

        if ($this->recipients !== null) {
            $body['recipients'] = $this->buildRecipientsBody($this->recipients);
        }

        foreach ($this->clearedFields as $clearedField) {
            $body[$clearedField->value] = null;
        }

        return $body;
    }

    /**
     * @param  list<RecipientRequestDTO>  $recipients
     * @return list<array<string, string|int|null>>
     */
    private function buildRecipientsBody(array $recipients): array
    {
        $recipientsBody = [];
        foreach ($recipients as $recipient) {
            $recipientsBody[] = $recipient->toRequestBody();
        }

        return $recipientsBody;
    }
}
