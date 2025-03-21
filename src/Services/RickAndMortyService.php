<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use NickBeen\RickAndMortyPhpApi\Api;
use NickBeen\RickAndMortyPhpApi\Exceptions\NotFoundException;

class RickAndMortyService
{
    private const RESOURCE = 'https://rickandmortyapi.com/api/episode/';
    private Client $client;

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
     * @return array|null
     * @throws NotFoundException
     */
    public function getEpisodes(): ?array
    {
        try {
            $episodes = $this->client->get(self::RESOURCE)->getBody();
        } catch (GuzzleException $e) {
            throw NotFoundException::resourceUnavailable();
        } catch (Exception) {
            return null;
        }

        return json_decode($episodes, true);
    }
}