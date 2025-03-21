<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function getAverageSentimentScore(int $episodeId): float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.sentimentScore)')
            ->andWhere('r.episodeId = :episodeId')
            ->setParameter('episodeId', $episodeId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
