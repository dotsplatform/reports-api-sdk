<?php

declare(strict_types=1);

/**
 * Description of CreatedReportRunDTO.php
 *
 * The response of POST runs: the run and, when a share link was asked for, its URL (shown only here).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class CreatedReportRunDTO extends DTO
{
    protected ReportRunDTO $run;

    protected ?string $shareUrl = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'run' => ReportRunDTO::fromResponse(ResponseField::array($data, 'data')),
            'shareUrl' => ResponseField::nullableString($data, 'share_url'),
        ]);
    }

    public function getRun(): ReportRunDTO
    {
        return $this->run;
    }

    public function getShareUrl(): ?string
    {
        return $this->shareUrl;
    }
}
