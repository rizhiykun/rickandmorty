<?php

namespace App\Services;

use App\DTO\Queries\IndexReviewQuery;
use App\DTO\Queries\PatchReviewQuery;
use App\DTO\Queries\UpdateReviewQuery;
use App\Entity\Review;
use App\Exception\AppException;
use App\Factory\ReviewFactory;
use App\Repository\ReviewRepository;

class ReviewService extends BaseService
{
    private const LAST_REVIEWS_LIMIT = 3;

    public function __construct(
        private readonly ReviewRepository         $reviewRepository,
        private readonly ReviewFactory            $reviewFactory,
        private readonly RickAndMortyService      $rickAndMortyService,
        private readonly SentimentAnalysisService $sentimentAnalysisService
    )
    {
    }

    public final function createReview(int $episodeId, string $reviewText): Review
    {
        $score = $this->sentimentAnalysisService->analyze($reviewText);
        $review = $this->reviewFactory->create($episodeId, $reviewText, $score);
        $this->reviewRepository->save($review);
        return $review;
    }

    /**
     * @throws AppException
     */
    public final function getSummary(int $episodeId): array
    {
        try {
            $episode = $this->rickAndMortyService->getEpisode($episodeId);
        } catch (\Exception $e) {
            throw new AppException('Ошибка при получении данных об эпизоде', 0, $e);
        }

        if (!$episode) {
            throw new AppException('Эпизод не найден');
        }

        $reviews = $this->reviewRepository->findBy([
            'episodeId' => $episodeId],
            ['id' => 'DESC'],
            self::LAST_REVIEWS_LIMIT
        );
        $averageScore = $this->reviewRepository->getAverageSentimentScore($episodeId);

        return [
            'episode_name' => $episode['name'],
            'release_date' => $episode['air_date'],
            'average_sentiment_score' => $averageScore,
            'last_reviews' => array_map(fn($r) => $r->getReviewText(), $reviews)
        ];
    }

    public final function listReviews(IndexReviewQuery $query): array
    {
        [$result, $count] = $this->reviewRepository->getIndex(
            $query->page,
            $query->perPage,
            $query->search
        );

        return $this->getResult($result, $count, $query->page, $query->perPage);
    }

    public final function updateReview(Review $review, UpdateReviewQuery $query): Review
    {
        $score = $this->sentimentAnalysisService->analyze($query->review);
        $review
            ->setReviewText($query->review)
            ->setEpisodeId($query->episodeId)
            ->setSentimentScore($score);

        $this->reviewRepository->save($review);

        return $review;
    }

    public final function removeReview(Review $review): void
    {
        $this->reviewRepository->removeById($review);
    }

    public final function patchReview(Review $review, PatchReviewQuery $query): Review
    {
        if ($query->episodeId) {
            $review->setEpisodeId($query->episodeId);
        }
        if ($query->review) {
            $score = $this->sentimentAnalysisService->analyze($query->review);
            $review
                ->setReviewText($query->review)
                ->setSentimentScore($score);
        }
        $this->reviewRepository->save($review);
        return $review;
    }

}