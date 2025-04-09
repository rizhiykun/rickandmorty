<?php

namespace App\DTO\Responses;

use OpenApi\Attributes as OA;
use Symfony\Component\PropertyInfo\Type;

#[OA\Schema(
    properties: [
        new OA\Property(property: "episode_name", type: Type::BUILTIN_TYPE_STRING, example: "Pilot"),
        new OA\Property(property: "release_date", type: Type::BUILTIN_TYPE_STRING, example: "December 2, 2013"),
        new OA\Property(property: "average_sentiment_score", type: Type::BUILTIN_TYPE_FLOAT, example: "0.5"),
        new OA\Property(
            property: "last_reviews",
            type: "array",
            items: new OA\Items(type: Type::BUILTIN_TYPE_STRING),
            example: [
                "Strange, bad cinema",
                "This is the best, beatiful cinema ever",
            ]
        ),
    ],
    type: "object"
)]
class EpisodeSummary
{
}
