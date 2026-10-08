<?php

declare(strict_types=1);

/**
 * Description of ReportDataDTO.php
 *
 * GET report-definitions/{id}/data: every visible block of a report, in layout order, for one period, so a client
 * can draw the report with its own charts.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Reports;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\PeriodDTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ReportDataDTO extends DTO
{
    protected string $name;

    protected string $scopeName;

    protected string $locale;

    protected PeriodDTO $period;

    protected ?string $comparedWith = null;

    protected int $dataAsOf;

    /** @var list<ReportBlockDataDTO> */
    protected array $blocks = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        $blocks = [];
        foreach (ResponseField::listOfObjects($data, 'blocks') as $blockData) {
            $blocks[] = ReportBlockDataDTO::fromResponse($blockData);
        }

        return static::fromArray([
            'name' => ResponseField::string($data, 'name'),
            'scopeName' => ResponseField::string($data, 'scope_name'),
            'locale' => ResponseField::string($data, 'locale'),
            'period' => PeriodDTO::fromResponse(ResponseField::array($data, 'period')),
            'comparedWith' => ResponseField::nullableString($data, 'compared_with'),
            'dataAsOf' => ResponseField::int($data, 'data_as_of'),
            'blocks' => $blocks,
        ]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScopeName(): string
    {
        return $this->scopeName;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Grain, key and the display label ("September 2026").
     */
    public function getPeriod(): PeriodDTO
    {
        return $this->period;
    }

    /**
     * The label of the comparison period, null without a comparison.
     */
    public function getComparedWith(): ?string
    {
        return $this->comparedWith;
    }

    /**
     * Unix timestamp of the newest data; 0 when nothing was computed yet.
     */
    public function getDataAsOf(): int
    {
        return $this->dataAsOf;
    }

    /**
     * @return list<ReportBlockDataDTO>
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }
}
