<?php

namespace App\DTO\Queries;

class EpisodesSummaryQuery
{
    public function __construct(
        public ?string $page = '1',
    ) {
    }
}
