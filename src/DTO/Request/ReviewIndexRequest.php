<?php

namespace App\DTO\Request;

use Symfony\Component\HttpFoundation\Request;

class ReviewIndexRequest implements RequestDTOInterface
{
    private int $count;
    private int $pages;
    public function __construct(
        Request $request
    ) {
        $this->count = (int)$request->get('count', 10);
        $this->pages = (int)$request->get('pages', 1);
    }

    public function getPage(): int
    {
        return $this->pages;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}