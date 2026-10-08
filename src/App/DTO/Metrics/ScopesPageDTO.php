<?php

declare(strict_types=1);

/**
 * Description of ScopesPageDTO.php
 *
 * One page of GET scopes. Pass getNextCursor() to the next call; null means this was the last page.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Metrics;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class ScopesPageDTO extends DTO
{
    /** @var list<ScopeEntryDTO> */
    protected array $scopes = [];

    protected ?string $nextCursor = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        $scopes = [];
        foreach (ResponseField::listOfObjects($data, 'data') as $scopeData) {
            $scopes[] = ScopeEntryDTO::fromResponse($scopeData);
        }

        return static::fromArray([
            'scopes' => $scopes,
            'nextCursor' => ResponseField::nullableString($data, 'next_cursor'),
        ]);
    }

    /**
     * @return list<ScopeEntryDTO>
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getNextCursor(): ?string
    {
        return $this->nextCursor;
    }

    public function hasNextPage(): bool
    {
        return $this->nextCursor !== null;
    }
}
