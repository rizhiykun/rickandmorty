<?php

namespace App\Services;

use App\Entity\Review;
use App\Factory\ReviewFactory;
use App\Repository\ReviewRepository;

class ReviewService
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private ReviewFactory $reviewFactory
    ) {}

    public final function createReview(int $episodeId, string $reviewText, float $sentimentScore): Review
    {
        $review = $this->reviewFactory->create($episodeId, $reviewText, $sentimentScore);
        $this->reviewRepository->save($review);
        return $review;
    }
}