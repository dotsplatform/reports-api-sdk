<?php

declare(strict_types=1);

/**
 * Description of ShareLinkDTO.php
 *
 * The response of POST share-links: a signed public URL of the report and when it expires.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ShareLinkDTO extends DTO
{
    protected string $runId;

    protected string $url;

    protected int $expiresAt;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'runId' => ResponseField::string($data, 'run_id'),
            'url' => ResponseField::string($data, 'url'),
            'expiresAt' => ResponseField::int($data, 'expires_at'),
        ]);
    }

    public function getRunId(): string
    {
        return $this->runId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Unix timestamp.
     */
    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }
}
