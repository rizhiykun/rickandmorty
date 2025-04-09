<?php

namespace App\DataFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/** @psalm-suppress UnusedClass */
class AppFixtures extends Fixture
{
    public const EPISODE_1_ID = 1;
    public const EPISODE_2_ID = 2;

    public const REVIEW_1_ID = '17b30016-39ea-470b-a074-c227abed9e05';
    public const REVIEW_2_ID = '17b30016-39ea-470b-a074-c227abed9e06';

    public const SENTIMENT_SCORE = 0.5;

    private Generator $faker;

    private Review $review;

    private Review $review2;

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $this->loadReviews($manager);

        $manager->flush();
    }

    private function loadReviews(ObjectManager $manager): void
    {
        $this->review = new Review();
        $this->review2 = new Review();

        $this->review
            ->setId(self::REVIEW_1_ID)
            ->setReviewText($this->faker->text())
            ->setEpisodeId(self::EPISODE_1_ID)
            ->setSentimentScore(self::SENTIMENT_SCORE);

        $this->review2
            ->setId(self::REVIEW_2_ID)
            ->setReviewText($this->faker->text())
            ->setEpisodeId(self::EPISODE_2_ID)
            ->setSentimentScore(self::SENTIMENT_SCORE);

        $manager->persist($this->review);
        $manager->persist($this->review2);
    }
}
