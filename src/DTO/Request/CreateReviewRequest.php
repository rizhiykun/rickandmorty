<?php

namespace App\DTO\Request;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateReviewRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Поле episodeId не может быть пустым')]
        #[Assert\Type(type: Type::BUILTIN_TYPE_INT, message: 'Поле episodeId должно быть числом')]
        public int $episodeId,
        #[Assert\NotBlank(message: 'Поле review не может быть пустым')]
        #[Assert\Type(type: Type::BUILTIN_TYPE_STRING, message: 'Поле review должно быть строкой')]
        public string $review
    )
    {
    }

}