<?php

declare(strict_types=1);

namespace App\DTO\Queries;

class EpisodesSummaryQuery
{
    public function __construct(
        public ?string $page = '1',
    ) {
    }
}
