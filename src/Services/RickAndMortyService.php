<?php

namespace App\Services;

use App\Exception\AppException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use NickBeen\RickAndMortyPhpApi\Exceptions\NotFoundException;

class RickAndMortyService
{
    private const RESOURCE = 'https://rickandmortyapi.com/api/episode/';
    private Client $client;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
    }

    /**
     * @param int $id
     * @return array|null
     * @throws NotFoundException
     */
    public function getEpisode(int $id): ?array
    {
        try {
            $episode = $this->client->get(self::RESOURCE . (string)$id)->getBody();
        } catch (GuzzleException $e) {
            throw NotFoundException::resourceUnavailable();
        } catch (Exception) {
            return null;
        }

        return json_decode((string)$episode, true);
    }

    /**
     * @param string|null $page
     * @return array|null
     * @throws AppException
     * @throws NotFoundException
     */
    public function getEpisodes(?string $page = null): ?array
    {
        $resource = $page ? self::RESOURCE . '?page=' . $page : self::RESOURCE;
        try {
            $episodes = $this->client->get($resource)->getBody();
        } catch (GuzzleException $e) {
            throw NotFoundException::resourceUnavailable();
        } catch (Exception) {
            return null;
        }

        $results = json_decode((string)$episodes, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AppException('Ошибка JSON: ' . json_last_error_msg());
        }

        $result = $results['results'];

        $result['pages'] = $results['info']['pages'];
        $result['count'] = $results['info']['count'];
        return $result;
    }
}