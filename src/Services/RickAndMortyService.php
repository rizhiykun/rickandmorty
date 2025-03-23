<?php

namespace App\Services;

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
     * Get a single episode by its ID.
     *
     * @param int $id
     * @return array|null
     * @throws NotFoundException
     */
    public function getEpisode(int $id): ?array
    {
        try {
            $episode = $this->client->get(self::RESOURCE . $id)->getBody();
        } catch (GuzzleException $e) {
            throw NotFoundException::resourceUnavailable();
        } catch (Exception) {
            return null;
        }

        return json_decode($episode, true);
    }

    /**
     * Get all episodes.
     *
     * @param int|null $page
     * @return array|null
     * @throws NotFoundException
     */
    public function getEpisodes(?int $page = null): ?array
    {
        $resource = $page ? self::RESOURCE . '?page=' . $page : self::RESOURCE;
        try {
            $episodes = $this->client->get($resource)->getBody();
        } catch (GuzzleException $e) {
            throw NotFoundException::resourceUnavailable();
        } catch (Exception) {
            return null;
        }

        $results = json_decode($episodes, true);

        $result = $results['results'];

        $result['pages'] = $results['info']['pages'];
        $result['count'] = $results['info']['count'];
        return $result;
    }
}