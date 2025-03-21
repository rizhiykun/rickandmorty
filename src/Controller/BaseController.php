<?php

namespace App\Controller;

use App\Enum\GroupsType;
use App\Services\AppSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

abstract class BaseController extends AbstractController
{

    public function __construct(
        private readonly AppSerializer $appSerializer
    ) {
    }
    public function appJson(
        mixed $data,
        int   $status = Response::HTTP_OK,
        array $headers = [],
        array $groups = [],
        array $context = [],
    ): Response {
        $groups = array_merge($groups, [GroupsType::BASE_FIELD]);

        $context = array_merge($context, [
            AbstractNormalizer::GROUPS => $groups,
        ]);

        return new Response(
            $this->appSerializer->serialize($data, 'json', $context),
            $status,
            array_merge($headers, [
                'content-type' => 'application/json',
            ]),
        );
    }
}