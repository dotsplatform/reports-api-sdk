<?php

declare(strict_types=1);

/**
 * Description of ReportDefinitionFormDTO.php
 *
 * The body of create, update (PATCH) and duplicate of report definitions. Every field is optional: a null field is
 * not sent, so the service keeps the current value (update) or uses the template value (create, duplicate).
 * A create with a templateKey starts from that platform template.
 *
 * To remove the fixed scope id in an update, call clear(ReportDefinitionClearableField::SCOPE_ID).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Reports\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Reports\LayoutBlockDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\Grain;
use Dotsplatform\ReportsApi\Enums\ReportDefinitionClearableField;
use Dotsplatform\ReportsApi\Enums\ReportPurpose;
use Dotsplatform\ReportsApi\Enums\ScopeType;

class ReportDefinitionFormDTO extends DTO
{
    protected ?string $templateKey = null;

    protected ?string $name = null;

    protected ?ReportPurpose $purpose = null;

    protected ?ScopeType $scopeType = null;

    protected ?string $scopeId = null;

    protected ?Grain $defaultGrain = null;

    protected ?CompareMode $comparison = null;

    protected ?int $trendPeriods = null;

    protected ?int $shareLinkDays = null;

    protected ?bool $isDefault = null;

    /**
     * Null keeps the layout; a list replaces it.
     *
     * @var list<LayoutBlockDTO>|null
     */
    protected ?array $blocks = null;

    /**
     * Fields sent as JSON null; set them only through clear().
     *
     * @var list<ReportDefinitionClearableField>
     */
    protected array $clearedFields = [];

    /**
     * A copy that also sends these fields as JSON null, so the service removes their values. A cleared field is
     * sent as null even when a value was set. Fields that are neither set nor cleared are not sent.
     */
    public function clear(ReportDefinitionClearableField ...$fields): static
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
     * @return list<ReportDefinitionClearableField>
     */
    public function getClearedFields(): array
    {
        return $this->clearedFields;
    }

    public function getTemplateKey(): ?string
    {
        return $this->templateKey;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPurpose(): ?ReportPurpose
    {
        return $this->purpose;
    }

    public function getScopeType(): ?ScopeType
    {
        return $this->scopeType;
    }

    public function getScopeId(): ?string
    {
        return $this->scopeId;
    }

    public function getDefaultGrain(): ?Grain
    {
        return $this->defaultGrain;
    }

    public function getComparison(): ?CompareMode
    {
        return $this->comparison;
    }

    public function getTrendPeriods(): ?int
    {
        return $this->trendPeriods;
    }

    public function getShareLinkDays(): ?int
    {
        return $this->shareLinkDays;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * @return list<LayoutBlockDTO>|null
     */
    public function getBlocks(): ?array
    {
        return $this->blocks;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestBody(): array
    {
        $body = [
            'template_key' => $this->templateKey,
            'name' => $this->name,
            'purpose' => $this->purpose?->value,
            'scope_type' => $this->scopeType?->value,
            'scope_id' => $this->scopeId,
            'default_grain' => $this->defaultGrain?->value,
            'comparison' => $this->comparison?->value,
            'trend_periods' => $this->trendPeriods,
            'share_link_days' => $this->shareLinkDays,
            'is_default' => $this->isDefault,
        ];

        $body = array_filter($body, fn (mixed $value): bool => $value !== null);

        if ($this->blocks !== null) {
            $body['layout'] = ['blocks' => $this->buildBlocksBody($this->blocks)];
        }

        foreach ($this->clearedFields as $clearedField) {
            $body[$clearedField->value] = null;
        }

        return $body;
    }

    /**
     * @param  list<LayoutBlockDTO>  $blocks
     * @return list<array{type: string, config: object}>
     */
    private function buildBlocksBody(array $blocks): array
    {
        $blocksBody = [];
        foreach ($blocks as $block) {
            $blocksBody[] = $block->toRequestBody();
        }

        return $blocksBody;
    }
}
