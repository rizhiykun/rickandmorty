<?php

namespace App\Tests\Unit\Controllers;

use App\Controller\EpisodesController;
use App\DTO\Queries\EpisodesSummaryQuery;
use App\Services\AppSerializer;
use App\Services\ReviewService;
use App\Services\RickAndMortyService;
use Codeception\PHPUnit\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\EpisodesController
 */
class EpisodesControllerTest extends TestCase
{
    private EpisodesController $controller;
    private MockObject $appSerializer;
    private MockObject $rickAndMortyService;
    private MockObject $reviewService;

    protected function setUp(): void
    {
        $this->appSerializer = $this->createMock(AppSerializer::class);
        $this->rickAndMortyService = $this->createMock(RickAndMortyService::class);
        $this->reviewService = $this->createMock(ReviewService::class);

        $this->controller = new EpisodesController(
            $this->appSerializer,
            $this->rickAndMortyService,
            $this->reviewService
        );
    }

    /**
     * @covers \App\Controller\EpisodesController::getEpisodeSummary
     */
    public function testGetEpisodeSummarySuccess(): void
    {
        $episodeId = 1;
        $summaryData = [
            'episode_name' => 'Pilot',
            'release_date' => '2013-12-02',
            'average_sentiment_score' => 0.8,
            'last_reviews' => ['Great episode!']
        ];

        $serializedData = json_encode($summaryData);

        $this->reviewService->expects($this->once())
            ->method('getSummary')
            ->with($episodeId)
            ->willReturn($summaryData);

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with($summaryData, 'json', $this->anything())
            ->willReturn($serializedData);

        $response = $this->controller->getEpisodeSummary($episodeId);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals($serializedData, $response->getContent());
    }

    /**
     * @covers \App\Controller\EpisodesController::getAllEpisodes
     */
    public function testGetAllEpisodesSuccess(): void
    {
        $query = new EpisodesSummaryQuery();
        $query->page = 1;

        $episodesData = [
            'results' => [
                ['id' => 1, 'name' => 'Pilot'],
                ['id' => 2, 'name' => 'Lawnmower Dog']
            ],
            'info' => ['pages' => 3, 'count' => 20]
        ];

        $serializedData = json_encode($episodesData);

        $this->rickAndMortyService->expects($this->once())
            ->method('getEpisodes')
            ->with($query->page)
            ->willReturn($episodesData);

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with($episodesData, 'json', $this->anything())
            ->willReturn($serializedData);

        $response = $this->controller->getAllEpisodes($query);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals($serializedData, $response->getContent());
    }

    /**
     * @covers \App\Controller\EpisodesController::getAllEpisodes
     */
    public function testGetAllEpisodesWithoutPage(): void
    {
        $query = new EpisodesSummaryQuery();
        $query->page = null;

        $episodesData = [
            'results' => [
                ['id' => 1, 'name' => 'Pilot']
            ],
            'info' => ['pages' => 1, 'count' => 1]
        ];

        $serializedData = json_encode($episodesData);

        $this->rickAndMortyService->expects($this->once())
            ->method('getEpisodes')
            ->with(null)
            ->willReturn($episodesData);

        $this->appSerializer->expects($this->once())
            ->method('serialize')
            ->with($episodesData, 'json', $this->anything())
            ->willReturn($serializedData);

        $response = $this->controller->getAllEpisodes($query);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals($serializedData, $response->getContent());
    }
}