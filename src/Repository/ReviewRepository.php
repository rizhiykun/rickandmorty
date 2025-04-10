<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends BaseRepository
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /** @psalm-suppress PossiblyUnusedParam */
    public function getAverageSentimentScore(int $episodeId): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.sentimentScore)')
            ->andWhere('r.episodeId = :episodeId')
            ->setParameter('episodeId', $episodeId)
            ->getQuery()
            ->getSingleScalarResult();
        return (float)$result;
    }

    /** @psalm-suppress PossiblyUnusedParam */
    public function getIndex(int $page, int $perPage, ?string $search): array
    {
        $qb = $this->createQueryBuilder('review');

        if (!empty($search)) {
            $qb
                ->andWhere('review.reviewText IS NOT NULL AND LOWER(review.reviewText) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');
        }

        $qb
            ->addOrderBy('review.createdAt', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage);

        $pagination = new Paginator($qb);

        $count = $pagination->count();
        $items = $qb->getQuery()
            ->getResult();

        return [$items, $count];
    }
}
