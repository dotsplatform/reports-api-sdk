<?php

declare(strict_types=1);

/**
 * Description of SeriesRequestDTO.php
 *
 * A series of POST metrics/query: the last N periods of a grain, the current one included
 * (e.g. the last 6 months for a trend chart).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics\Requests;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\Grain;

class SeriesRequestDTO extends DTO
{
    protected Grain $grain;

    protected int $last;

    public static function last(int $periodsCount, Grain $grain): static
    {
        return static::fromArray(['grain' => $grain, 'last' => $periodsCount]);
    }

    public function getGrain(): Grain
    {
        return $this->grain;
    }

    public function getLast(): int
    {
        return $this->last;
    }

    /**
     * @return array{grain: string, last: int}
     */
    public function toRequestBody(): array
    {
        return [
            'grain' => $this->grain->value,
            'last' => $this->last,
        ];
    }
}
