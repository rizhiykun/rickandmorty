<?php

namespace App\Controller;

use App\DTO\Queries\EpisodesSummaryQuery;
use App\DTO\Responses\Episodes;
use App\DTO\Responses\EpisodeSummary;
use App\Services\AppSerializer;
use App\Services\ReviewService;
use App\Services\RickAndMortyService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/', name: 'episodes_')]
#[OA\Tag('Episodes')]
class EpisodesController extends BaseController
{

    public function __construct(
        AppSerializer                             $appSerializer,
        private readonly RickAndMortyService      $rickAndMortyService,
        private readonly ReviewService            $reviewService
    )
    {
        parent::__construct($appSerializer);
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
}