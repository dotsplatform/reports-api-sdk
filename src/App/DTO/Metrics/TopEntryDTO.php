<?php

declare(strict_types=1);

/**
 * Description of TopEntryDTO.php
 *
 * One row of a top list (top items, venues, promo codes): the entity uuid, its label and its measures. Measures
 * ending in "_cents" are money.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\DTO\Common\MoneyDTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class TopEntryDTO extends DTO
{
    protected string $id;

    protected ?string $label = null;

    /** @var array<string, int|float|MoneyDTO|null> */
    protected array $values = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'label' => ResponseField::nullableString($data, 'label'),
            'values' => self::readValues(ResponseField::array($data, 'values')),
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return array<string, int|float|MoneyDTO|null>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * A plain measure (units, orders); money measures return their cents.
     */
    public function getNumber(string $measureKey): int|float|null
    {
        $value = $this->values[$measureKey] ?? null;
        if ($value instanceof MoneyDTO) {
            return $value->getAmountCents();
        }

        return $value;
    }

    public function getMoney(string $measureKey): ?MoneyDTO
    {
        $value = $this->values[$measureKey] ?? null;
        if ($value instanceof MoneyDTO) {
            return $value;
        }

        return null;
    }

    /**
     * @param  array<array-key, mixed>  $valuesData
     * @return array<string, int|float|MoneyDTO|null>
     */
    private static function readValues(array $valuesData): array
    {
        $values = [];
        foreach ($valuesData as $measureKey => $value) {
            $measureKey = (string) $measureKey;
            if (is_array($value)) {
                $values[$measureKey] = MoneyDTO::fromResponse($value);

                continue;
            }

            $values[$measureKey] = ResponseField::nullableNumber($valuesData, $measureKey);
        }

        return $values;
    }
}
