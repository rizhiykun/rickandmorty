<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    /** @psalm-suppress PossiblyUnusedParam */
    public function save($entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @psalm-suppress PossiblyUnusedParam */
    /** @psalm-suppress PossiblyUnusedMethod */
    public function remove($entity, bool $flush = true): mixed
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    /** @psalm-suppress PossiblyUnusedParam */
    public function removeById($id, bool $flush = true): void
    {
        $entity = $this->find($id);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}