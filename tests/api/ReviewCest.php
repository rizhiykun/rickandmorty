<?php

namespace App\Tests\api;

use App\DataFixtures\AppFixtures;
use App\Tests\Support\ApiTester;
use Codeception\Example;
use Faker\Factory;
use Faker\Generator;

class ReviewCest
{
    private const URL_CONTROLLER = '/api/review';

    private Generator $faker;


    public function __construct()
    {
        $this->faker = Factory::create();
    }

    protected function createReview(): array
    {
        return [
            'fields' => [
                'episodeId' => 3,
                'review' => $this->faker->text(),
                ]
        ];
    }

    protected function getEpisodeSummary(): array
    {
        return [
            'id' => AppFixtures::EPISODE_1_ID
        ];
    }

    protected function getAllEpisodes(): array
    {
        return [
          'data' => [
              'page' => 1
              ]
        ];
    }

    protected function listReview(): array
    {
        return [
            'data' => [
                'page' => 1,
                'perPage' => 20,
                'search' => ''
            ]
        ];
    }

    protected function getReview(): array
    {
        return [
            'id' => AppFixtures::REVIEW_1_ID
        ];
    }

    protected function removeReview()
    {
        return [
            'id' => AppFixtures::REVIEW_1_ID
        ];
    }

    protected function updateReview(): array
    {
        return [
            'id' => AppFixtures::REVIEW_1_ID,
            'fields' => [
                'episodeId' => 3,
                'review' => $this->faker->text(),
            ]
        ];
    }

    protected function patchReview(): array
    {
        return [
            'id' => AppFixtures::REVIEW_1_ID,
            'fields' => [
                'episodeId' => 3,
                'review' => $this->faker->text(),
            ]
        ];
    }

    /**
     * @param ApiTester $I
     * @param Example $example
     * @dataProvider createReview
     */
    public function createReviewMethod(ApiTester $I, Example $example): void
    {
//        $I->wantTo('Сохранить обзор: ' . $example['description']);
        $I->sendPost(self::URL_CONTROLLER . "/create", $example['fields']);
//        $I->seeResponseCodeIs($example['code']);
//        if ($example['code'] !== HttpCode::OK) {
//            $I->seeResponseContains($example['error']);
//        }
    }


}