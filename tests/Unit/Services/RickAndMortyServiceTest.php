<?php

namespace App\Tests\Unit\Services;

use App\Services\RickAndMortyService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use NickBeen\RickAndMortyPhpApi\Exceptions\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Services\RickAndMortyService
 */
class RickAndMortyServiceTest extends TestCase
{
    private RickAndMortyService $service;
    private Client|MockObject $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём мок клиента
        $this->client = $this->createMock(Client::class);
        $this->service = new RickAndMortyService();
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisode
     */
    public function testGetEpisodeSuccess(): void
    {
        $id = 1;
        $responseBody = json_encode([
            'id' => $id,
            'name' => 'Pilot',
            'air_date' => 'December 2, 2013',
            'episode' => 'S01E01'
        ]);

        // Мокируем StreamInterface, чтобы он возвращал строку как тело ответа
        $stream = $this->createMock(Stream::class);
        $stream->method('__toString')->willReturn($responseBody);

        // Мокируем ResponseInterface
        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($stream);

        // Мокируем Client
        $this->client->method('get')->willReturn($response);

        $episode = $this->service->getEpisode($id);

        $this->assertNotNull($episode);
        $this->assertEquals($id, $episode['id']);
        $this->assertEquals('Pilot', $episode['name']);
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisode
     */
    public function testGetEpisodeNotFound(): void
    {
        $this->client->method('get')->willThrowException(new RequestException(
        'Error message',
        new Request('GET', 'test')
    ));

        $this->expectException(NotFoundException::class);

        $this->service->getEpisode(999);
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisodes
     */
    public function testGetEpisodesSuccess(): void
    {
        $page = 1;
        $responseBody = json_encode([
            'info' => ['pages' => 10, 'count' => 100],
            'results' => [
                ['id' => 1, 'name' => 'Pilot'],
                ['id' => 2, 'name' => 'Lawn Mower Dog']
            ]
        ]);

        // Мокируем StreamInterface, чтобы он возвращал строку как тело ответа
        $stream = $this->createMock(Stream::class);
        $stream->method('__toString')->willReturn($responseBody);

        // Мокируем ResponseInterface
        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($stream);

        // Мокируем Client
        $this->client->method('get')->willReturn($response);

        $episodes = $this->service->getEpisodes($page);

        $this->assertNotNull($episodes);
        $this->assertCount(22, $episodes);
        $this->assertEquals(3, $episodes['pages']);
        $this->assertEquals(51, $episodes['count']);
    }

}