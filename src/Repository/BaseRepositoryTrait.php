<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\{
    NonUniqueResultException,
    NoResultException
};

trait BaseRepositoryTrait
{
    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countAll(): int
    {
        $count = $this->getEntityManager()
            ->createQuery(sprintf('SELECT COUNT(a) FROM %s a', $this->getClassName()))
            ->getSingleScalarResult();

        return (int) $count;
    }
}
