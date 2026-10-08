<?php

declare(strict_types=1);

/**
 * Description of LayoutBlockDTO.php
 *
 * One block of a report layout (task §8.2): its type and its config, checked by the service against the block
 * schema (GET blocks).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Reports;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class LayoutBlockDTO extends DTO
{
    protected string $type;

    /** @var array<string, mixed> */
    protected array $config = [];

    /**
     * @param  array<string, mixed>  $config
     */
    public static function create(string $type, array $config = []): static
    {
        return static::fromArray(['type' => $type, 'config' => $config]);
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'type' => ResponseField::string($data, 'type'),
            'config' => ResponseField::object($data, 'config'),
        ]);
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return array{type: string, config: object}
     */
    public function toRequestBody(): array
    {
        return [
            'type' => $this->type,
            // An empty config must stay a JSON object.
            'config' => (object) $this->config,
        ];
    }
}
