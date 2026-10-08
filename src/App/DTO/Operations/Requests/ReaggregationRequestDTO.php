<?php

declare(strict_types=1);

/**
 * Description of ReaggregationRequestDTO.php
 *
 * The body of POST reaggregate: which accounts (none = all), local date range and grains (none = all) to
 * re-compute snapshots for.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Operations\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\Grain;

class ReaggregationRequestDTO extends DTO
{
    /** @var list<string> */
    protected array $accountIds = [];

    protected ?string $fromDate = null;

    protected ?string $toDate = null;

    /** @var list<Grain> */
    protected array $grains = [];

    /**
     * @return list<string>
     */
    public function getAccountIds(): array
    {
        return $this->accountIds;
    }

    public function getFromDate(): ?string
    {
        return $this->fromDate;
    }

    public function getToDate(): ?string
    {
        return $this->toDate;
    }

    /**
     * @return list<Grain>
     */
    public function getGrains(): array
    {
        return $this->grains;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestBody(): array
    {
        $body = [];
        if ($this->accountIds !== []) {
            $body['accounts'] = $this->accountIds;
        }

        if ($this->fromDate !== null) {
            $body['from'] = $this->fromDate;
        }

        if ($this->toDate !== null) {
            $body['to'] = $this->toDate;
        }

        if ($this->grains !== []) {
            $body['grains'] = $this->buildGrainValues();
        }

        return $body;
    }

    /**
     * @return list<string>
     */
    private function buildGrainValues(): array
    {
        $grainValues = [];
        foreach ($this->grains as $grain) {
            $grainValues[] = $grain->value;
        }

        return $grainValues;
    }
}
