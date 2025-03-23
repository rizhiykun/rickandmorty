<?php

namespace App\DTO\Queries;

use Symfony\Component\Validator\Constraints as Assert;

class IndexReviewQuery
{
    public function __construct(
        public ?int    $page = 1,
        public ?int    $perPage = 20,
        public ?string $search = '',
    ) {
    }
}