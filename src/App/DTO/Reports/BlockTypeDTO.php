<?php

declare(strict_types=1);

/**
 * Description of BlockTypeDTO.php
 *
 * One block type of GET blocks: its key, its family (the picker group) and its config schema.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Reports;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class BlockTypeDTO extends DTO
{
    protected string $type;

    protected string $family;

    /** @var array<string, mixed> */
    protected array $configSchema = [];

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'type' => ResponseField::string($data, 'type'),
            'family' => ResponseField::string($data, 'family'),
            'configSchema' => ResponseField::object($data, 'config_schema'),
        ]);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFamily(): string
    {
        return $this->family;
    }

    /**
     * Config field => its type ("string", "integer", a list of allowed values, …).
     *
     * @return array<string, mixed>
     */
    public function getConfigSchema(): array
    {
        return $this->configSchema;
    }
}
