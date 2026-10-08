<?php

declare(strict_types=1);

/**
 * Description of BackfillRequestDTO.php
 *
 * The body of POST backfill: the local date range to re-sync for one account; null ends mean the service default.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Operations\Requests;

use Dots\Data\DTO;

class BackfillRequestDTO extends DTO
{
    protected ?string $fromDate = null;

    protected ?string $toDate = null;

    public function getFromDate(): ?string
    {
        return $this->fromDate;
    }

    public function getToDate(): ?string
    {
        return $this->toDate;
    }

    /**
     * @return array<string, string>
     */
    public function toRequestBody(): array
    {
        $body = [];
        if ($this->fromDate !== null) {
            $body['from'] = $this->fromDate;
        }

        if ($this->toDate !== null) {
            $body['to'] = $this->toDate;
        }

        return $body;
    }
}
