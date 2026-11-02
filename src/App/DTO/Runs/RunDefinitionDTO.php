<?php

declare(strict_types=1);

/**
 * Description of RunDefinitionDTO.php
 *
 * The definition a run was made from, as the account's list of runs names it: id, name, purpose and the library
 * layout's key (null for an account's own definition built from scratch).
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Enums\ReportPurpose;
use Dotsplatform\ReportsApi\Support\ResponseField;

class RunDefinitionDTO extends DTO
{
    protected string $id;

    protected string $name;

    protected string $purposeValue;

    protected ?string $templateKey = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        return static::fromArray([
            'id' => ResponseField::string($data, 'id'),
            'name' => ResponseField::string($data, 'name'),
            'purposeValue' => ResponseField::string($data, 'purpose'),
            'templateKey' => ResponseField::nullableString($data, 'template_key'),
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * The raw value, also when this SDK version does not know it.
     */
    public function getPurposeValue(): string
    {
        return $this->purposeValue;
    }

    /**
     * Null when the service sends a value this SDK version does not know (see getPurposeValue()).
     */
    public function getPurpose(): ?ReportPurpose
    {
        return ReportPurpose::tryFrom($this->purposeValue);
    }

    public function getTemplateKey(): ?string
    {
        return $this->templateKey;
    }
}
