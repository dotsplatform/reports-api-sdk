<?php

declare(strict_types=1);

/**
 * Description of AccountRunDTO.php
 *
 * One run of GET accounts/{account}/runs: the run, its definition and its period as the report page names it in the
 * account's language ("Жовтень 2026"). The list never carries share links: getRun()->getShareUrl() is null here, and
 * hasShareLink() says whether the run has one; ReportRunsClient::get() returns it.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class AccountRunDTO extends DTO
{
    protected ReportRunDTO $run;

    protected RunDefinitionDTO $definition;

    protected string $periodLabel;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'run' => ReportRunDTO::fromResponse($data),
            'definition' => RunDefinitionDTO::fromResponse(ResponseField::array($data, 'definition')),
            'periodLabel' => ResponseField::string($data, 'period_label'),
        ]);
    }

    public function getRun(): ReportRunDTO
    {
        return $this->run;
    }

    public function getDefinition(): RunDefinitionDTO
    {
        return $this->definition;
    }

    public function getPeriodLabel(): string
    {
        return $this->periodLabel;
    }
}
