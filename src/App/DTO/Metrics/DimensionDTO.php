<?php

declare(strict_types=1);

/**
 * Description of DimensionDTO.php
 *
 * One breakdown dimension of GET dimensions (delivery type, payment, weekday, …).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class DimensionDTO extends DTO
{
    protected string $key;

    protected string $label;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'key' => ResponseField::string($data, 'key'),
            'label' => ResponseField::string($data, 'label'),
        ]);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
