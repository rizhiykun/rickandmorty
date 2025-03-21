<?php

namespace App\Controller;

use App\Entity\Review;
use App\Enum\GroupsType;
use App\Repository\ReviewRepository;
use App\Services\AppSerializer;
use App\Services\ReviewService;
use App\Services\RickAndMortyService;
use App\Services\SentimentAnalysisService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/review')]
#[OA\Tag('Apartment')]
class ReviewController extends BaseController
{
    public function __construct(
        AppSerializer                             $appSerializer,
        private readonly RickAndMortyService      $rickAndMortyService,
        private readonly SentimentAnalysisService $sentimentAnalysisService,
        private readonly ReviewRepository         $reviewRepository,
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
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(property: 'result', properties: [
                    new OA\Property(property: 'item', ref: new Model(type: Review::class, groups: [
                        GroupsType::BASE_FIELD,
                    ])),
                ], type: 'object'),
            ]
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'review', type: 'string', example: 'Text for example'),
                new OA\Property(property: 'episode_id', type: 'int', example: '123')
            ]
        )
    )]
    public function submitReview(Request $request, array $groups = [GroupsType::BASE_FIELD]
    ): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['episode_id'], $data['review'])) {
            return $this->appJson(['error' => 'Missing episode_id or review'], 400);
        }

        // Perform sentiment analysis
        $score = $this->sentimentAnalysisService->analyze($data['review']);
        $this->reviewService->createReview($data['episode_id'], $data['review'], $score);

        //TODO: drop down to service layer
        return $this->appJson(['message' => 'Review submitted', 'sentiment_score' => $score], groups: $groups);
    }

    #[Route('/episode/{id}/summary', methods: [Request::METHOD_GET])]
    public function getEpisodeSummary(int $id): Response
    {
        $episode = $this->rickAndMortyService->getEpisode($id);
        if (!$episode) {
            return $this->json(['error' => 'Episode not found'], 404);
        }

        $reviews = $this->reviewRepository->findBy(['episodeId' => $id], ['id' => 'DESC'], 3);
        $averageScore = $this->reviewRepository->getAverageSentimentScore($id);

        return $this->appJson([
            'episode_name' => $episode['name'],
            'release_date' => $episode['air_date'],
            'average_sentiment_score' => $averageScore,
            'last_reviews' => array_map(fn($r) => $r->getReviewText(), $reviews)
        ]);
    }

    #[Route('/episodes', methods: [Request::METHOD_GET])]
    public function getAllEpisodes(): Response
    {
        return $this->appJson($this->rickAndMortyService->getEpisodes());
    }
}
