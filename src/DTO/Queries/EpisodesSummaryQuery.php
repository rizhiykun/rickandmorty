<?php

namespace App\DTO\Queries;

class EpisodesSummaryQuery
{
    public function __construct(
        public ?int $page = 1,
    ) {
    }
}