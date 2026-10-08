<?php

declare(strict_types=1);

/**
 * Description of SubscriptionDTO.php
 *
 * A report subscription (task §10.1): which report, for which scope and grain, when, in which format and to whom.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Subscriptions;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\SubscriptionChannel;
use Dotsplatform\ReportsApi\Enums\SubscriptionFormat;
use Dotsplatform\ReportsApi\Support\ResponseField;

class SubscriptionDTO extends DTO
{
    protected string $id;

    protected string $definitionId;

    protected ScopeDTO $scope;

    protected Grain $grain;

    protected SubscriptionScheduleDTO $schedule;

    protected SubscriptionChannel $channel;

    protected SubscriptionFormat $format;

    protected ?string $locale = null;

    protected bool $isActive;

    protected ?string $pausedReason = null;

    protected ?int $nextDueAt = null;

    protected ?int $lastSentAt = null;

    protected ?string $lastPeriodKey = null;

    protected ?string $createdBy = null;

    /** @var list<SubscriptionRecipientDTO> */
    protected array $recipients = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'definitionId' => ResponseField::string($data, 'definition_id'),
            'scope' => ScopeDTO::fromResponse(ResponseField::array($data, 'scope')),
            'grain' => ResponseField::enum($data, 'grain', Grain::class),
            'schedule' => SubscriptionScheduleDTO::fromResponse(ResponseField::array($data, 'schedule')),
            'channel' => ResponseField::enum($data, 'channel', SubscriptionChannel::class),
            'format' => ResponseField::enum($data, 'format', SubscriptionFormat::class),
            'locale' => ResponseField::nullableString($data, 'locale'),
            'isActive' => ResponseField::bool($data, 'is_active'),
            'pausedReason' => ResponseField::nullableString($data, 'paused_reason'),
            'nextDueAt' => ResponseField::nullableInt($data, 'next_due_at'),
            'lastSentAt' => ResponseField::nullableInt($data, 'last_sent_at'),
            'lastPeriodKey' => ResponseField::nullableString($data, 'last_period_key'),
            'createdBy' => ResponseField::nullableString($data, 'created_by'),
            'recipients' => self::readRecipients($data),
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDefinitionId(): string
    {
        return $this->definitionId;
    }

    public function getScope(): ScopeDTO
    {
        return $this->scope;
    }

    public function getGrain(): Grain
    {
        return $this->grain;
    }

    public function getSchedule(): SubscriptionScheduleDTO
    {
        return $this->schedule;
    }

    public function getChannel(): SubscriptionChannel
    {
        return $this->channel;
    }

    public function getFormat(): SubscriptionFormat
    {
        return $this->format;
    }

    /**
     * Null: the account report locale.
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Why the service paused the subscription (e.g. every recipient suppressed); null when not paused.
     */
    public function getPausedReason(): ?string
    {
        return $this->pausedReason;
    }

    /**
     * Unix timestamp of the next send; null when inactive.
     */
    public function getNextDueAt(): ?int
    {
        return $this->nextDueAt;
    }

    /**
     * Unix timestamp.
     */
    public function getLastSentAt(): ?int
    {
        return $this->lastSentAt;
    }

    public function getLastPeriodKey(): ?string
    {
        return $this->lastPeriodKey;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    /**
     * @return list<SubscriptionRecipientDTO>
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return list<SubscriptionRecipientDTO>
     */
    private static function readRecipients(array $data): array
    {
        $recipients = [];
        foreach (ResponseField::listOfObjects($data, 'recipients') as $recipientData) {
            $recipients[] = SubscriptionRecipientDTO::fromResponse($recipientData);
        }

        return $recipients;
    }
}
