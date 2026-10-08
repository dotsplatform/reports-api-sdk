<?php

declare(strict_types=1);

/**
 * Description of ReportRunDTO.php
 *
 * A report run (task §8.1): one frozen render of a definition for a scope and period, with its PDF state. The
 * share URL is only in the create response (CreatedReportRunDTO).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\PeriodDTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\ReportRunStatus;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ReportRunDTO extends DTO
{
    protected string $id;

    protected string $definitionId;

    protected ScopeDTO $scope;

    protected PeriodDTO $period;

    protected ?string $compareValue = null;

    protected bool $isAnonymised;

    protected string $statusValue;

    protected bool $hasShareLink;

    protected ?int $expiresAt = null;

    protected int $createdAt;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'definitionId' => ResponseField::string($data, 'definition_id'),
            'scope' => ScopeDTO::fromResponse(ResponseField::array($data, 'scope')),
            'period' => PeriodDTO::fromResponse(ResponseField::array($data, 'period')),
            'compareValue' => ResponseField::nullableString($data, 'compare'),
            'isAnonymised' => ResponseField::bool($data, 'is_anonymised'),
            'statusValue' => ResponseField::string($data, 'status'),
            'hasShareLink' => ResponseField::bool($data, 'has_share_link'),
            'expiresAt' => ResponseField::nullableInt($data, 'expires_at'),
            'createdAt' => ResponseField::int($data, 'created_at'),
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

    public function getPeriod(): PeriodDTO
    {
        return $this->period;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getCompareValue(): ?string
    {
        return $this->compareValue;
    }

    /**
     * Null without a value, or when the service sends one this SDK version does not know (see getCompareValue()).
     */
    public function getCompare(): ?CompareMode
    {
        if ($this->compareValue === null) {
            return null;
        }

        return CompareMode::tryFrom($this->compareValue);
    }

    public function isAnonymised(): bool
    {
        return $this->isAnonymised;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getStatusValue(): string
    {
        return $this->statusValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getStatusValue()).
     */
    public function getStatus(): ?ReportRunStatus
    {
        return ReportRunStatus::tryFrom($this->statusValue);
    }

    public function isPdfReady(): bool
    {
        return $this->getStatus() === ReportRunStatus::PDF_READY;
    }

    public function hasShareLink(): bool
    {
        return $this->hasShareLink;
    }

    /**
     * Unix timestamp when the share link stops working; null without a share link.
     */
    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    /**
     * Unix timestamp.
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }
}
