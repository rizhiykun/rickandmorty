<?php

namespace App\Services;

use App\Exception\AppException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class BaseService
{
    public function __construct() {}


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

//    /**
//     * @param $entity
//     * @param array|null $groups
//     * @return array
//     * @throws AppException
//     */
//    public function convertEntityToArray($entity, ?array $groups = []): array
//    {
//        try {
//            $context = [
//                'groups' => $groups,
//                'circular_reference_handler' => function ($object) {
//                    return $object->getId();
//                },
//            ];
//
//            $normalizedData = $this->normalizer->normalize($entity, null, $context);
//
//            if (!is_array($normalizedData)) {
//                $normalizedData = (array)$normalizedData;
//            }
//
//            return $normalizedData;
//        } catch (ExceptionInterface $e) {
//            throw new AppException($e->getMessage());
//        }
//    }
}
