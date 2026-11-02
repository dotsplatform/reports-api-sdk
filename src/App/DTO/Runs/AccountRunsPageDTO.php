<?php

declare(strict_types=1);

/**
 * Description of AccountRunsPageDTO.php
 *
 * One page of GET accounts/{account}/runs, newest first. Pass getNextCursor() to the next call; null means this was
 * the last page.
 *
 * @copyright Copyright (c) DOTSPLATFORM, LLC
 * @author    Bogdan Mamontov <bohdan.mamontov@dotsplatform.com>
 */

namespace Dotsplatform\ReportsApi\DTO\Runs;

use Dots\Data\DTO;
use Dotsplatform\ReportsApi\Support\ResponseField;

class AccountRunsPageDTO extends DTO
{
    /** @var list<AccountRunDTO> */
    protected array $runs = [];

    protected ?string $nextCursor = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromResponse(array $data): static
    {
        $runs = [];
        foreach (ResponseField::listOfObjects($data, 'data') as $runData) {
            $runs[] = AccountRunDTO::fromResponse($runData);
        }

        return static::fromArray([
            'runs' => $runs,
            'nextCursor' => ResponseField::nullableString($data, 'next_cursor'),
        ]);
    }

    /**
     * @return list<AccountRunDTO>
     */
    public function getRuns(): array
    {
        return $this->runs;
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
