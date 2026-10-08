<?php

declare(strict_types=1);

/**
 * Description of CreateReportRunRequestDTO.php
 *
 * The body of POST runs: the report period plus whether to create a share link and render a PDF.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs\Requests;

class CreateReportRunRequestDTO extends ReportPeriodRequestDTO
{
    protected bool $withShareLink = false;

    protected bool $withPdf = false;

    public function isWithShareLink(): bool
    {
        return $this->withShareLink;
    }

    public function isWithPdf(): bool
    {
        return $this->withPdf;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestBody(): array
    {
        $body = parent::toRequestBody();
        $body['share_link'] = $this->withShareLink;
        $body['pdf'] = $this->withPdf;

        return $body;
    }
}
