<?php

namespace App\Tests\Unit\Services;

use App\DTO\Queries\IndexReviewQuery;
use App\DTO\Queries\PatchReviewQuery;
use App\DTO\Queries\UpdateReviewQuery;
use App\Entity\Review;
use App\Factory\ReviewFactory;
use App\Repository\ReviewRepository;
use App\Services\ReviewService;
use App\Services\RickAndMortyService;
use App\Services\SentimentAnalysisService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReviewServiceTest extends TestCase
{
    private ReviewService $reviewService;
    private MockObject $reviewRepository;
    private MockObject $reviewFactory;
    private MockObject $rickAndMortyService;
    private MockObject $sentimentAnalysisService;

    protected function setUp(): void
    {
        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->reviewFactory = $this->createMock(ReviewFactory::class);
        $this->rickAndMortyService = $this->createMock(RickAndMortyService::class);
        $this->sentimentAnalysisService = $this->createMock(SentimentAnalysisService::class);

        $this->reviewService = new ReviewService(
            $this->reviewRepository,
            $this->reviewFactory,
            $this->rickAndMortyService,
            $this->sentimentAnalysisService
        );
    }

    public function testCreateReview(): void
    {
        $episodeId = 1;
        $reviewText = 'Great episode!';
        $sentimentScore = 0.9;
        $review = $this->createMock(Review::class);

        $this->sentimentAnalysisService->method('analyze')->with($reviewText)->willReturn($sentimentScore);
        $this->reviewFactory->method('create')->with($episodeId, $reviewText, $sentimentScore)->willReturn($review);
        $this->reviewRepository->expects($this->once())->method('save')->with($review);

        $result = $this->reviewService->createReview($episodeId, $reviewText);
        $this->assertSame($review, $result);
    }

    public function testGetSummary(): void
    {
        $episodeId = 1;
        $episodeData = ['name' => 'Pilot', 'air_date' => '2013-12-02'];
        $reviews = [$this->createMock(Review::class)];

        $this->rickAndMortyService->method('getEpisode')->with($episodeId)->willReturn($episodeData);
        $this->reviewRepository->method('findBy')->willReturn($reviews);
        $this->reviewRepository->method('getAverageSentimentScore')->willReturn(0.8);

        $result = $this->reviewService->getSummary($episodeId);

        $this->assertSame('Pilot', $result['episode_name']);
        $this->assertSame('2013-12-02', $result['release_date']);
        $this->assertSame(0.8, $result['average_sentiment_score']);
    }

    public function testUpdateReview(): void
    {
        $review = $this->createMock(Review::class);
        $query = new UpdateReviewQuery(1, 'Updated review text');

        $this->sentimentAnalysisService->method('analyze')->willReturn(0.7);

        $review->expects($this->once())->method('setReviewText')->with($query->review);

        $this->reviewRepository->expects($this->once())->method('save')->with($review);

        $result = $this->reviewService->updateReview($review, $query);
        $this->assertSame($review, $result);
    }

    public function testRemoveReview(): void
    {
        $review = $this->createMock(Review::class);

        $this->reviewRepository->expects($this->once())->method('removeById')->with($review);

        $this->reviewService->removeReview($review);
    }

    public function testListReviews(): void
    {
        $query = new IndexReviewQuery(1, 10, 'test');
        $mockReviews = [$this->createMock(Review::class)];

        $this->reviewRepository->method('getIndex')->willReturn([$mockReviews, 1]);

        $result = $this->reviewService->listReviews($query);

        $this->assertArrayHasKey('items', $result);
    }

    public function testPatchReview(): void
    {
        $review = $this->createMock(Review::class);
        $query = new PatchReviewQuery(1, 'Patched review text');

        $this->sentimentAnalysisService->method('analyze')->willReturn(0.6);

        $review->expects($this->once())->method('setReviewText')->with($query->review);

        $this->reviewRepository->expects($this->once())->method('save')->with($review);

        $result = $this->reviewService->patchReview($review, $query);
        $this->assertSame($review, $result);
    }
}
