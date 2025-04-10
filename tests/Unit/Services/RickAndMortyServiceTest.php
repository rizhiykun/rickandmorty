<?php

namespace App\Tests\Unit\Services;

use App\Exception\AppException;
use App\Services\RickAndMortyService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use NickBeen\RickAndMortyPhpApi\Exceptions\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers \App\Services\RickAndMortyService
 */
class RickAndMortyServiceTest extends TestCase
{
    public const URL = 'https://test.url';
    private RickAndMortyService $service;
    private Client|MockObject $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);
        $this->service = new RickAndMortyService(self::URL);

        // Инъекция мока клиента через рефлексию
        $reflection = new ReflectionClass($this->service);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->service, $this->client);
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

        $stream = $this->createMock(Stream::class);
        $stream->method('__toString')->willReturn($responseBody);

        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($stream);

        $this->client->method('get')->willReturn($response);

        $episode = $this->service->getEpisode($id);

        $this->assertNotNull($episode);
        $this->assertEquals($id, $episode['id']);
        $this->assertEquals('Pilot', $episode['name']);
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisode
     */
    public function testGetEpisodeReturnsNullOnGenericException(): void
    {
        $this->client->method('get')->willThrowException(new \Exception());

        $result = $this->service->getEpisode(1);

        $this->assertNull($result);
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisode
     */
    public function testGetEpisodeThrowsNotFoundExceptionOnGuzzleException(): void
    {
        $this->client->method('get')->willThrowException(
            new RequestException('Error', new Request('GET', 'test'))
        );

        $this->expectException(NotFoundException::class);

        $this->service->getEpisode(1);
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisodes
     */
    public function testGetEpisodesSuccess(): void
    {
        $responseData = [
            'info' => ['pages' => 3, 'count' => 51],
            'results' => [
                ['id' => 1, 'name' => 'Pilot'],
                ['id' => 2, 'name' => 'Lawn Mower Dog']
            ]
        ];
        $responseBody = json_encode($responseData);

        $stream = $this->createMock(Stream::class);
        $stream->method('__toString')->willReturn($responseBody);

        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($stream);

        $this->client->method('get')->willReturn($response);

        $result = $this->service->getEpisodes('1');

        $this->assertNotNull($result);
        $this->assertCount(4, $result); // results count
        $this->assertEquals(3, $result['pages']);
        $this->assertEquals(51, $result['count']);
        $this->assertEquals('Pilot', $result[0]['name']);
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisodes
     */
    public function testGetEpisodesWithoutPage(): void
    {
        $responseData = [
            'info' => ['pages' => 1, 'count' => 2],
            'results' => [
                ['id' => 1, 'name' => 'Pilot']
            ]
        ];
        $responseBody = json_encode($responseData);

        $stream = $this->createMock(Stream::class);
        $stream->method('__toString')->willReturn($responseBody);

        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($stream);

        $this->client->expects($this->once())
            ->method('get')
            ->with(self::URL)
            ->willReturn($response);

        $result = $this->service->getEpisodes();

        $this->assertNotNull($result);
        $this->assertCount(3, $result);
    }
    /**
     * @covers \App\Services\RickAndMortyService::getEpisodes
     */
    public function testGetEpisodesThrowsNotFoundException(): void
    {
        $this->client->method('get')->willThrowException(
            new RequestException('Error', new Request('GET', 'test'))
        );

        $this->expectException(NotFoundException::class);

        $this->service->getEpisodes();
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisodes
     */
    public function testGetEpisodesReturnsNullOnGenericException(): void
    {
        $this->client->method('get')->willThrowException(new \Exception());

        $result = $this->service->getEpisodes();

        $this->assertNull($result);
    }

    /**
     * @covers \App\Services\RickAndMortyService::getEpisodes
     */
    public function testGetEpisodesThrowsAppExceptionOnInvalidJson(): void
    {
        $stream = $this->createMock(Stream::class);
        $stream->method('__toString')->willReturn('invalid json');

        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturn($stream);

        $this->client->method('get')->willReturn($response);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Ошибка JSON');

        $this->service->getEpisodes();
    }

}