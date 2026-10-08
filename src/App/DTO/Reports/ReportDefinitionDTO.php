<?php

declare(strict_types=1);

/**
 * Description of ReportDefinitionDTO.php
 *
 * A report definition (task §8.1). Platform templates have isTemplate() = true: an account reads and copies
 * them but cannot change them.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Reports;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportPurpose;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ReportDefinitionDTO extends DTO
{
    protected string $id;

    protected string $name;

    protected bool $isTemplate;

    protected ?string $templateKey = null;

    protected string $purposeValue;

    protected ScopeDTO $scope;

    protected string $defaultGrainValue;

    protected string $comparisonValue;

    protected int $trendPeriods;

    protected int $shareLinkDays;

    protected bool $isDefault;

    protected ?string $createdBy = null;

    /** @var list<LayoutBlockDTO> */
    protected array $blocks = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'name' => ResponseField::string($data, 'name'),
            'isTemplate' => ResponseField::bool($data, 'is_template'),
            'templateKey' => ResponseField::nullableString($data, 'template_key'),
            'purposeValue' => ResponseField::string($data, 'purpose'),
            'scope' => ScopeDTO::fromResponse(ResponseField::array($data, 'scope')),
            'defaultGrainValue' => ResponseField::string($data, 'default_grain'),
            'comparisonValue' => ResponseField::string($data, 'comparison'),
            'trendPeriods' => ResponseField::int($data, 'trend_periods'),
            'shareLinkDays' => ResponseField::int($data, 'share_link_days'),
            'isDefault' => ResponseField::bool($data, 'is_default'),
            'createdBy' => ResponseField::nullableString($data, 'created_by'),
            'blocks' => self::readBlocks(ResponseField::array($data, 'layout')),
        ]);
    }

    /**
     * @param  list<array<array-key, mixed>>  $definitionsData
     * @return list<ReportDefinitionDTO>
     */
    public static function listFromResponse(array $definitionsData): array
    {
        $definitions = [];
        foreach ($definitionsData as $definitionData) {
            $definitions[] = self::fromResponse($definitionData);
        }

        return $definitions;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isTemplate(): bool
    {
        return $this->isTemplate;
    }

    public function getTemplateKey(): ?string
    {
        return $this->templateKey;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getPurposeValue(): string
    {
        return $this->purposeValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getPurposeValue()).
     */
    public function getPurpose(): ?ReportPurpose
    {
        return ReportPurpose::tryFrom($this->purposeValue);
    }

    public function getScope(): ScopeDTO
    {
        return $this->scope;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getDefaultGrainValue(): string
    {
        return $this->defaultGrainValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getDefaultGrainValue()).
     */
    public function getDefaultGrain(): ?Grain
    {
        return Grain::tryFrom($this->defaultGrainValue);
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getComparisonValue(): string
    {
        return $this->comparisonValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getComparisonValue()).
     */
    public function getComparison(): ?CompareMode
    {
        return CompareMode::tryFrom($this->comparisonValue);
    }

    public function getTrendPeriods(): int
    {
        return $this->trendPeriods;
    }

    public function getShareLinkDays(): int
    {
        return $this->shareLinkDays;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * The X-Actor-Id of the call that created the definition.
     */
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    /**
     * @return list<LayoutBlockDTO>
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param  array<array-key, mixed>  $layoutData
     * @return list<LayoutBlockDTO>
     */
    private static function readBlocks(array $layoutData): array
    {
        $blocks = [];
        foreach (ResponseField::listOfObjects($layoutData, 'blocks') as $blockData) {
            $blocks[] = LayoutBlockDTO::fromResponse($blockData);
        }

        return $blocks;
    }
}
