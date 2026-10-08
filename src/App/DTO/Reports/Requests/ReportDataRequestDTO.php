<?php

declare(strict_types=1);

/**
 * Description of ReportDataRequestDTO.php
 *
 * The query of GET report-definitions/{id}/data. Every field is optional: without a scope the definition scope is
 * used, without a grain and period the last closed period of the definition default grain.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Reports\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\ScopeDTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\Grain;

class ReportDataRequestDTO extends DTO
{
    protected ?ScopeDTO $scope = null;

    protected ?Grain $grain = null;

    protected ?string $periodKey = null;

    protected ?CompareMode $compare = null;

    public function getScope(): ?ScopeDTO
    {
        return $this->scope;
    }

    public function getGrain(): ?Grain
    {
        return $this->grain;
    }

    /**
     * "2026-09", "2026-W41", "2026-10-08" or "2025", matching the grain.
     */
    public function getPeriodKey(): ?string
    {
        return $this->periodKey;
    }

    public function getCompare(): ?CompareMode
    {
        return $this->compare;
    }

    /**
     * @return array<string, string>
     */
    public function toQuery(): array
    {
        $query = [];
        if ($this->scope !== null) {
            $query['scope_type'] = $this->scope->getScopeTypeValue();
            $query['scope_id'] = (string) $this->scope->getScopeId();
        }

        if ($this->grain !== null) {
            $query['grain'] = $this->grain->value;
        }

        if ($this->periodKey !== null) {
            $query['period'] = $this->periodKey;
        }

        if ($this->compare !== null) {
            $query['compare'] = $this->compare->value;
        }

        return $query;
    }
}
