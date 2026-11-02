<?php

declare(strict_types=1);

/**
 * Description of ListAccountRunsRequestDTO.php
 *
 * The filters and page of GET accounts/{account}/runs: optional definition, grain and period key, the cursor of the
 * previous page and the page size (1–50, the service's default 20).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\Grain;

class ListAccountRunsRequestDTO extends DTO
{
    protected ?string $definitionId = null;

    protected ?Grain $grain = null;

    protected ?string $periodKey = null;

    protected ?string $cursor = null;

    protected ?int $limit = null;

    public function getDefinitionId(): ?string
    {
        return $this->definitionId;
    }

    public function getGrain(): ?Grain
    {
        return $this->grain;
    }

    public function getPeriodKey(): ?string
    {
        return $this->periodKey;
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Only the filters that are set.
     *
     * @return array<string, string|int>
     */
    public function toQuery(): array
    {
        $query = [];
        if ($this->definitionId !== null) {
            $query['definition_id'] = $this->definitionId;
        }

        if ($this->grain !== null) {
            $query['grain'] = $this->grain->value;
        }

        if ($this->periodKey !== null) {
            $query['period_key'] = $this->periodKey;
        }

        if ($this->cursor !== null) {
            $query['cursor'] = $this->cursor;
        }

        if ($this->limit !== null) {
            $query['limit'] = $this->limit;
        }

        return $query;
    }
}
