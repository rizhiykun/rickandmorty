<?php

namespace App\Factory;

use App\Entity\Review;

class ReviewFactory
{
    public function create(
        int $episodeId,
        string $reviewText,
        float $sentimentScore
    ): Review {
        $review = new Review();

        $review
            ->setEpisodeId($episodeId)
            ->setReviewText($reviewText)
            ->setSentimentScore($sentimentScore)
        ;

        return $review;
    }
}
