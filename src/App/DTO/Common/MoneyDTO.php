<?php

declare(strict_types=1);

/**
 * Description of MoneyDTO.php
 *
 * A money value (task §4.3): an integer of minor units (cents, kopiyky, tetri) and its ISO 4217 currency.
 * JSON: {"amount_cents": 113600, "currency": "UAH"}.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Common;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class MoneyDTO extends DTO
{
    protected int $amountCents;

    protected string $currency;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'amountCents' => ResponseField::int($data, 'amount_cents'),
            'currency' => ResponseField::string($data, 'currency'),
        ]);
    }

    public function getAmountCents(): int
    {
        return $this->amountCents;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
