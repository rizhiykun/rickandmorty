<?php

namespace App\Controller;

use App\DTO\Queries\EpisodesSummaryQuery;
use App\DTO\Queries\IndexReviewQuery;
use App\DTO\Queries\PatchReviewQuery;
use App\DTO\Queries\UpdateReviewQuery;
use App\DTO\Request\CreateReviewRequest;
use App\DTO\Responses\Episodes;
use App\DTO\Responses\EpisodeSummary;
use App\Entity\Review;
use App\Enum\GroupsType;
use App\Services\AppSerializer;
use App\Services\ReviewService;
use App\Services\RickAndMortyService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;


#[Route('/', name: 'review_')]
#[OA\Tag('Review')]
class ReviewController extends BaseController
{
    const REVIEW_GROUPS = [GroupsType::BASE_FIELD, GroupsType::REVIEW];
    public function __construct(
        AppSerializer                             $appSerializer,
        private readonly RickAndMortyService      $rickAndMortyService,
        private readonly ReviewService            $reviewService
    )
    {
        parent::__construct($appSerializer);
    }

    #[Route('/create', methods: [Request::METHOD_POST])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает созданный объект.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: Review::class, groups: self::REVIEW_GROUPS)),
                ], type: Type::BUILTIN_TYPE_OBJECT),
            ]
        )
    )]
    #[OA\RequestBody(content: new Model(type: CreateReviewRequest::class))]
    public function submitReview(
        #[MapRequestPayload]
        CreateReviewRequest $request
    ): Response
    {
        return $this->appJson(
            $this->reviewService->createReview($request->episodeId, $request->review),
            groups: self::REVIEW_GROUPS
        );
    }

    #[Route('/episode/{id}/summary', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает сводную информацию об эпизоде.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: EpisodeSummary::class)),
                ], type: Type::BUILTIN_TYPE_OBJECT),
            ]
        )
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'ID эпизода',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: "12"
    )]
    public function getEpisodeSummary(int $id): Response
    {
        return $this->appJson($this->reviewService->getSummary($id));
    }

    #[Route('/episodes', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: "Успешный ответ с информацией об эпизодах",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: Episodes::class)),
                ], type: 'object'),
            ]
        )
    )]
    #[OA\QueryParameter(name: 'page', description: 'Номер страницы', example: 1)]
    public function getAllEpisodes(
        #[MapQueryString]
        EpisodesSummaryQuery $query
    ): Response
    {
        return $this->appJson($this->rickAndMortyService->getEpisodes($query->page));
    }

    #[Route(methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: "Список с информацией об обзорах",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: Review::class, groups: self::REVIEW_GROUPS)),
                ], type: Type::BUILTIN_TYPE_OBJECT),
                new OA\Property(
                    property: 'pagination',
                    type: Type::BUILTIN_TYPE_ARRAY,
                    items: new OA\Items(properties: [
                        new OA\Property(property: 'page', type: Type::BUILTIN_TYPE_INT),
                        new OA\Property(property: 'perPage', type: Type::BUILTIN_TYPE_INT),
                        new OA\Property(property: 'search', type: Type::BUILTIN_TYPE_STRING),
                    ])
                ),
            ]
        )
    )]
    public function listReviews(
        #[MapQueryString]
        IndexReviewQuery $query
    ): Response
    {
        return $this->appJson($this->reviewService->listReviews($query), groups: self::REVIEW_GROUPS);
    }

    #[Route('/{id}',methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: "Возвращает информацию об обзоре",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: Review::class, groups: self::REVIEW_GROUPS)),
                ], type: Type::BUILTIN_TYPE_OBJECT),
            ]
        )
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'ID обзора',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: "3fa85f64-5717-4562-b3fc-2c963f66afa6"
    )]
    public function getReview(?Review $review): Response
    {
        $this->handleReviewNotFound($review);
        return $this->appJson($review, groups: self::REVIEW_GROUPS);
    }

    #[Route('/{id}',methods: [Request::METHOD_DELETE])]
    #[OA\Response(
        response: 200,
        description: "Удаляет обзор",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
            ]
        )
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'ID обзора',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: "3fa85f64-5717-4562-b3fc-2c963f66afa6"
    )]
    public function removeReview(?Review $review): Response
    {
        $this->handleReviewNotFound($review);

        $this->reviewService->removeReview($review);
        return $this->appJson(['success' => true]);
    }

    #[Route('/{id}',methods: [Request::METHOD_PUT])]
    #[OA\Response(
        response: 200,
        description: "Возвращает информацию об обновленном обзоре",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: Review::class, groups: self::REVIEW_GROUPS)),
                ], type: Type::BUILTIN_TYPE_OBJECT),
            ]
        )
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'ID обзора',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: "3fa85f64-5717-4562-b3fc-2c963f66afa6"
    )]
    public function updateReview(
        #[MapQueryString]
        UpdateReviewQuery $query,
        ?Review $review
    ): Response
    {
        $this->handleReviewNotFound($review);

        return $this->appJson(
            $this->reviewService->updateReview($review, $query),
            groups: self::REVIEW_GROUPS
        );
    }

    #[Route('/{id}',methods: [Request::METHOD_PATCH])]
    #[OA\Response(
        response: 200,
        description: "Возвращает информацию обновленном об обзоре",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: Review::class, groups: self::REVIEW_GROUPS)),
                ], type: Type::BUILTIN_TYPE_OBJECT),
            ]
        )
    )]
    #[OA\PathParameter(
        name: 'id',
        description: 'ID обзора',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: "3fa85f64-5717-4562-b3fc-2c963f66afa6"
    )]
    public function patchReview(
        #[MapQueryString]
        PatchReviewQuery $query,
        ?Review $review
    ): Response
    {
        $this->handleReviewNotFound($review);

        return $this->appJson(
            $this->reviewService->patchReview($review, $query),
            groups: self::REVIEW_GROUPS
        );
    }
}
