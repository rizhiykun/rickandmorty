<?php

namespace App\Tests\Unit\Controllers;

use App\Controller\ReviewController;
use App\DTO\Queries\IndexReviewQuery;
use App\DTO\Queries\PatchReviewQuery;
use App\DTO\Queries\UpdateReviewQuery;
use App\DTO\Request\CreateReviewRequest;
use App\Entity\Review;
use App\Enum\GroupsType;
use App\Services\AppSerializer;
use App\Services\ReviewService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @covers \App\Controller\ReviewController
 * @covers \App\Controller\BaseController
 * @covers \App\DTO\Request\CreateReviewRequest
 * @covers \App\DTO\Queries\IndexReviewQuery
 * @covers \App\DTO\Queries\UpdateReviewQuery
 * @covers \App\DTO\Queries\PatchReviewQuery
 * @covers \App\DTO\Queries\EpisodesSummaryQuery
 * @covers \App\Entity\Review
 * @covers \App\Services\ReviewService
 * @covers \App\Services\AppSerializer
 */
class ReviewControllerTest extends TestCase
{
    private ReviewController $controller;
    private MockObject $appSerializer;
    private MockObject $reviewService;

    protected function setUp(): void
    {
        $this->appSerializer = $this->createMock(AppSerializer::class);
        $this->reviewService = $this->createMock(ReviewService::class);

        $this->controller = new ReviewController(
            $this->appSerializer,
            $this->reviewService
        );
    }

    /**
     * @covers \App\Controller\ReviewController::submitReview
     */
    public function testSubmitReview(): void
    {
        $request = new CreateReviewRequest(
            episodeId: 1,
            review: 'Great episode!'
        );


        $review = $this->createMock(Review::class);
        $serializedData = json_encode(['success' => true, 'result' => ['item' => $review]]);

        $this->reviewService->expects($this->once())
            ->method('createReview')
            ->with($request->episodeId, $request->review)
            ->willReturn($review);

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->identicalTo($review),
                $this->equalTo('json'),
                $this->equalTo([AbstractNormalizer::GROUPS => ['baseField', 'review', 'baseField']])
            )
            ->willReturn($serializedData);

        $response = $this->controller->submitReview($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals($serializedData, $response->getContent());
        $this->assertNotEmpty($response->getContent(), 'Response content should not be empty');
        $this->assertJson($response->getContent(), 'Response should be a valid JSON');
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('result', $responseData);
        $this->assertArrayHasKey('item', $responseData['result']);
    }

    /**
     * @covers \App\Controller\ReviewController::listReviews
     */
    public function testListReviews(): void
    {
        $query = new IndexReviewQuery();
        $query->page = 1;
        $query->perPage = 10;
        $query->search = 'test';

        $reviewsData = ['items' => [], 'pagination' => []];
        $serializedData = '{"success":true,"result":{"item":[]}}';

        $this->reviewService->expects($this->once())
            ->method('listReviews')
            ->with($query)
            ->willReturn($reviewsData);

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with($reviewsData, 'json')
            ->willReturn($serializedData);

        $response = $this->controller->listReviews($query);

        $this->assertInstanceOf(Response::class, $response, 'Response should be instance of Response');
        $this->assertEquals(200, $response->getStatusCode(), 'Status code should be 200');
        $this->assertNotEmpty($response->getContent(), 'Response content should not be empty');
        $this->assertJson($response->getContent(), 'Response should be a valid JSON');
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $responseData, 'Response should have success key');
    }

    /**
     * @covers \App\Controller\ReviewController::getReview
     */
    public function testGetReview(): void
    {
        $review = $this->createMock(Review::class);
        $serializedData = json_encode(['success' => true, 'result' => ['item' => $review]]);

        $this->reviewService->expects($this->once())
            ->method('handleReviewNotFound')
            ->with($this->identicalTo($review));

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->identicalTo($review),
                $this->equalTo('json'),
                $this->equalTo([AbstractNormalizer::GROUPS => ['baseField', 'review', 'baseField']])
            )
            ->willReturn($serializedData);

        $response = $this->controller->getReview($review);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals($serializedData, $response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertNotEmpty($response->getContent(), 'Response content should not be empty');
        $this->assertJson($response->getContent(), 'Response should be a valid JSON');
        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertArrayHasKey('result', $responseData);
        $this->assertArrayHasKey('item', $responseData['result']);
    }

    /**
     * @covers \App\Controller\ReviewController::updateReview
     */
    public function testUpdateReview(): void
    {
        $review = $this->createMock(Review::class);
        $query = new UpdateReviewQuery(
            episodeId: 1,
            review: 'Updated review'
        );

        $serializedData = json_encode(['success' => true, 'result' => ['item' => $review]]);

        $this->reviewService->expects($this->once())
            ->method('updateReview')
            ->with(
                $this->identicalTo($review),
                $this->identicalTo($query))
            ->willReturn($review);

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->identicalTo($review),
                $this->equalTo('json'),
                $this->equalTo([AbstractNormalizer::GROUPS => ['baseField', 'review', 'baseField']])
            )
            ->willReturn($serializedData);

        $response = $this->controller->updateReview($query, $review);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals($serializedData, $response->getContent());
        $this->assertNotEmpty($response->getContent(), 'Response content should not be empty');
        $this->assertJson($response->getContent(), 'Response should be a valid JSON');
        $this->assertArrayHasKey('success', json_decode($response->getContent(), true));
    }

    /**
     * @covers \App\Controller\ReviewController::patchReview
     */
    public function testPatchReview(): void
    {
        $review = $this->createMock(Review::class);
        $query = new PatchReviewQuery(
            episodeId: 1,
            review: 'Patched review'
        );


        $serializedData = json_encode(['success' => true, 'result' => ['item' => $review]]);

        $this->reviewService->expects($this->once())
            ->method('patchReview')
            ->with(
                $this->identicalTo($review),
                $this->identicalTo($query))
            ->willReturn($review);

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->identicalTo($review),
                $this->equalTo('json'),
                $this->equalTo([AbstractNormalizer::GROUPS => ['baseField', 'review', 'baseField']])
            )
            ->willReturn($serializedData);

        $response = $this->controller->patchReview($query, $review);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals($serializedData, $response->getContent());
        $this->assertNotEmpty($response->getContent(), 'Response content should not be empty');
        $this->assertJson($response->getContent(), 'Response should be a valid JSON');
        $this->assertArrayHasKey('success', json_decode($response->getContent(), true));
    }
}