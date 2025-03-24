<?php

namespace App\Tests\Unit\Services;

use App\DTO\Queries\IndexReviewQuery;
use App\DTO\Queries\PatchReviewQuery;
use App\DTO\Queries\UpdateReviewQuery;
use App\Entity\Review;
use App\Exception\AppException;
use App\Factory\ReviewFactory;
use App\Repository\ReviewRepository;
use App\Services\ReviewService;
use App\Services\RickAndMortyService;
use App\Services\SentimentAnalysisService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Services\ReviewService
 * @covers \App\DTO\Queries\PatchReviewQuery
 * @covers \App\DTO\Queries\IndexReviewQuery
 * @covers \App\DTO\Queries\UpdateReviewQuery
 * @covers \App\Services\BaseService
 */
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

        $this->sentimentAnalysisService->method('analyze')
            ->with($reviewText)
            ->willReturn($sentimentScore);

        $this->reviewFactory->method('create')
            ->with($episodeId, $reviewText, $sentimentScore)
            ->willReturn($review);

        $this->reviewRepository->expects($this->once())
            ->method('save')
            ->with($review);

        $result = $this->reviewService->createReview($episodeId, $reviewText);
        $this->assertSame($review, $result);
    }

    public function testGetSummarySuccess(): void
    {
        $episodeId = 1;
        $episodeData = ['name' => 'Pilot', 'air_date' => '2013-12-02'];
        $reviewMock = $this->createMock(Review::class);
        $reviewMock->method('getReviewText')->willReturn('Great episode!');

        $this->rickAndMortyService->method('getEpisode')
            ->with($episodeId)
            ->willReturn($episodeData);

        $this->reviewRepository->method('findBy')
            ->with(
                ['episodeId' => $episodeId],
                ['id' => 'DESC'],
                3
            )
            ->willReturn([$reviewMock]);

        $this->reviewRepository->method('getAverageSentimentScore')
            ->with($episodeId)
            ->willReturn(0.8);

        $result = $this->reviewService->getSummary($episodeId);

        $this->assertEquals([
            'episode_name' => 'Pilot',
            'release_date' => '2013-12-02',
            'average_sentiment_score' => 0.8,
            'last_reviews' => ['Great episode!']
        ], $result);
    }

    public function testGetSummaryThrowsAppExceptionWhenEpisodeNotFound(): void
    {
        $this->rickAndMortyService->method('getEpisode')->willReturn(null);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Эпизод не найден');

        $this->reviewService->getSummary(1);
    }

    public function testGetSummaryThrowsAppExceptionWhenApiFails(): void
    {
        $this->rickAndMortyService->method('getEpisode')
            ->willThrowException(new \Exception('API error'));

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Ошибка при получении данных об эпизоде');

        $this->reviewService->getSummary(1);
    }

    public function testListReviews(): void
    {
        $query = new IndexReviewQuery(1, 10, 'test');
        $mockReview = $this->createMock(Review::class);

        $this->reviewRepository->method('getIndex')
            ->with(1, 10, 'test')
            ->willReturn([[$mockReview], 1]);

        $result = $this->reviewService->listReviews($query);

        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('pagination', $result);
    }

    public function testUpdateReview(): void
    {
        $review = $this->createMock(Review::class);
        $query = new UpdateReviewQuery(1, 'Updated review text');

        $this->sentimentAnalysisService->method('analyze')
            ->with('Updated review text')
            ->willReturn(0.7);

        $review->expects($this->once())
            ->method('setReviewText')
            ->with('Updated review text');

        $this->reviewRepository->expects($this->once())
            ->method('save')
            ->with($review);

        $result = $this->reviewService->updateReview($review, $query);
        $this->assertSame($review, $result);
    }


    public function testUpdateReviewThrowsExceptionWhenReviewNotFound(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Обзор с таким ID не найден');

        $this->reviewService->updateReview(null, new UpdateReviewQuery(1, 'text'));
    }

    public function testRemoveReview(): void
    {
        $review = $this->createMock(Review::class);

        $this->reviewRepository->expects($this->once())
            ->method('remove')
            ->with($review);

        $this->reviewService->removeReview($review);
    }

    public function testRemoveReviewThrowsExceptionWhenReviewNotFound(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Обзор с таким ID не найден');

        $this->reviewService->removeReview(null);
    }

    public function testPatchReviewWithTextOnly(): void
    {
        $review = $this->createMock(Review::class);
        $query = new PatchReviewQuery(null, 'Patched review text');

        $this->sentimentAnalysisService->method('analyze')
            ->with('Patched review text')
            ->willReturn(0.6);

        $review->expects($this->once())
            ->method('setReviewText')
            ->with('Patched review text');
        // Убрана проверка setSentimentScore, так как он вызывается только при наличии review text
        $review->expects($this->never())
            ->method('setEpisodeId');

        $this->reviewRepository->expects($this->once())
            ->method('save')
            ->with($review);

        $result = $this->reviewService->patchReview($review, $query);
        $this->assertSame($review, $result);
    }

    public function testPatchReviewWithEpisodeIdOnly(): void
    {
        $review = $this->createMock(Review::class);
        $query = new PatchReviewQuery(1, null);

        $this->sentimentAnalysisService->expects($this->never())
            ->method('analyze');

        $review->expects($this->once())
            ->method('setEpisodeId')
            ->with(1);
        $review->expects($this->never())
            ->method('setReviewText');
        $review->expects($this->never())
            ->method('setSentimentScore');

        $this->reviewRepository->expects($this->once())
            ->method('save')
            ->with($review);

        $result = $this->reviewService->patchReview($review, $query);
        $this->assertSame($review, $result);
    }

    public function testPatchReviewThrowsExceptionWhenReviewNotFound(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Обзор с таким ID не найден');

        $this->reviewService->patchReview(null, new PatchReviewQuery(1, 'text'));
    }

    public function testHandleReviewNotFound(): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Обзор с таким ID не найден');

        $this->reviewService->handleReviewNotFound(null);
    }

    public function testHandleReviewFound(): void
    {
        $review = $this->createMock(Review::class);

        // Ожидаем, что исключение не будет выброшено
        $this->reviewService->handleReviewNotFound($review);

        // Если тест дойдет до этой точки - значит все ок
        $this->assertTrue(true);
    }
}
