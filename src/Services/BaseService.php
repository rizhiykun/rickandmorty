<?php

namespace App\Services;

use App\Exception\AppException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
