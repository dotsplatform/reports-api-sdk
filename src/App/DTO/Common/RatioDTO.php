<?php

declare(strict_types=1);

/**
 * Description of RatioDTO.php
 *
 * A ratio or mean (task §6.2): both parts and the computed value. For money ratios (average check) the value is
 * in cents. JSON: {"numerator": 86, "denominator": 1990, "value": 0.0432}.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Common;

use Dots\Data\DTO;

class RatioDTO extends DTO
{
    protected ?int $numerator;

    protected int $denominator;

    protected int|float|null $value;

    public function getNumerator(): ?int
    {
        return $this->numerator;
    }

    public function getDenominator(): int
    {
        return $this->denominator;
    }

    /**
     * Null when the denominator is 0.
     */
    public function getValue(): int|float|null
    {
        return $this->value;
    }
}
