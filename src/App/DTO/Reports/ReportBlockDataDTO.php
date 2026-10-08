<?php

declare(strict_types=1);

/**
 * Description of ReportBlockDataDTO.php
 *
 * The resolved data of one report block (GET report-definitions/{id}/data). The data shape belongs to the block
 * type; values are already formatted for display where the block shows text.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Reports;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ReportBlockDataDTO extends DTO
{
    protected string $type;

    protected string $title;

    /** @var array<string, mixed> */
    protected array $data = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'type' => ResponseField::string($data, 'type'),
            'title' => ResponseField::string($data, 'title'),
            'data' => ResponseField::object($data, 'data'),
        ]);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
