<?php

namespace App\DTO\Responses;

use OpenApi\Attributes as OA;
use Symfony\Component\PropertyInfo\Type;

#[OA\Schema(
    schema: "Episodes",
    properties: [
        new OA\Property(property: "id", type: Type::BUILTIN_TYPE_INT, example: 1),
        new OA\Property(property: "name", type: Type::BUILTIN_TYPE_STRING, example: "Pilot"),
        new OA\Property(property: "air_date", type: Type::BUILTIN_TYPE_STRING, example: "December 2, 2013"),
        new OA\Property(property: "episode", type: Type::BUILTIN_TYPE_STRING, example: "S01E01"),
        new OA\Property(
            property: "characters",
            type: "array",
            items: new OA\Items(type: Type::BUILTIN_TYPE_STRING),
            example: [
                "https://rickandmortyapi.com/api/character/1",
                "https://rickandmortyapi.com/api/character/2",
            ]
        ),
        new OA\Property(property: "url", type: Type::BUILTIN_TYPE_STRING, example: "https://rickandmortyapi.com/api/episode/1"),
        new OA\Property(property: "created", type: Type::BUILTIN_TYPE_STRING, example: "2017-11-10T12:56:33.798Z"),
    ],
    type: "object"
)]
class Episodes
{
}
