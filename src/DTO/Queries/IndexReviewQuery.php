<?php

namespace App\DTO\Queries;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

class IndexReviewQuery
{
    public function __construct(
        #[Assert\NotBlank(message: 'Поле page не может быть пустым')]
        #[Assert\Type(type: Type::BUILTIN_TYPE_INT, message: 'Поле page должно быть числом')]
        public int    $page = 1,
        #[Assert\NotBlank(message: 'Поле page не может быть пустым')]
        #[Assert\Type(type: Type::BUILTIN_TYPE_INT, message: 'Поле page должно быть числом')]
        public int    $perPage = 20,
        public ?string $search = '',
    ) {
    }
}
