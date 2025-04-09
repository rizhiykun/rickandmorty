<?php

namespace App\DTO\Queries;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

class PatchReviewQuery
{
    public function __construct(
        #[Assert\Type(type: Type::BUILTIN_TYPE_INT, message: 'Поле episodeId должно быть числом')]
        public ?int $episodeId,
        #[Assert\Type(
            type: Type::BUILTIN_TYPE_STRING,
            message: 'Поле review должно быть строкой'
        )]
        public ?string $review
    ) {
    }
}
