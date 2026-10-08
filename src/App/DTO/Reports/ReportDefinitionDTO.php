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

    protected ReportPurpose $purpose;

    protected ScopeDTO $scope;

    protected Grain $defaultGrain;

    protected CompareMode $comparison;

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
            'purpose' => ResponseField::enum($data, 'purpose', ReportPurpose::class),
            'scope' => ScopeDTO::fromResponse(ResponseField::array($data, 'scope')),
            'defaultGrain' => ResponseField::enum($data, 'default_grain', Grain::class),
            'comparison' => ResponseField::enum($data, 'comparison', CompareMode::class),
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

    public function getPurpose(): ReportPurpose
    {
        return $this->purpose;
    }

    public function getScope(): ScopeDTO
    {
        return $this->scope;
    }

    public function getDefaultGrain(): Grain
    {
        return $this->defaultGrain;
    }

    public function getComparison(): CompareMode
    {
        return $this->comparison;
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
