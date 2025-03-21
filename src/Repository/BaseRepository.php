<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function save($entity, bool $flush = true): void
    {
        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove($entity, bool $flush = true): mixed
    {
        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function removeById($id, bool $flush = true): true
    {
        $entity = $this->find($id);

        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }

        return true;
    }

}