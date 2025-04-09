<?php

namespace App\Services;

abstract class BaseService
{
    public function getResult(array $result, int $count, int $page, int $perPage): array
    {
        return [
            'items' => $result,
            'pagination' => [
                'page' => $page,
                'pages' => (int)ceil($count / $perPage),
                'totalCount' => $count,
                'perPage' => $perPage,
            ],
        ];
    }
}
