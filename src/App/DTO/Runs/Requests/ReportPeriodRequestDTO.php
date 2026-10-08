<?php

declare(strict_types=1);

/**
 * Description of ReportPeriodRequestDTO.php
 *
 * The body of POST share-links, and the base of POST runs: which period of the report, anonymised or not, and
 * the comparison (null: the definition comparison).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\CompareMode;
use Dotsplatform\ReportsApi\Enums\Grain;

class ReportPeriodRequestDTO extends DTO
{
    protected Grain $grain;

    protected string $periodKey;

    protected bool $isAnonymised = false;

    protected ?CompareMode $compare = null;

    public function getGrain(): Grain
    {
        return $this->grain;
    }

    public function getPeriodKey(): string
    {
        return $this->periodKey;
    }

    public function isAnonymised(): bool
    {
        return $this->isAnonymised;
    }

    public function getCompare(): ?CompareMode
    {
        return $this->compare;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestBody(): array
    {
        $body = [
            'grain' => $this->grain->value,
            'period_key' => $this->periodKey,
            'is_anonymised' => $this->isAnonymised,
        ];

        if ($this->compare !== null) {
            $body['compare'] = $this->compare->value;
        }

        return $body;
    }
}
