<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\DBAL\{
    Connection,
    Exception
};
use Doctrine\ORM\{
    EntityManager,
    Exception\NotSupported,
    OptimisticLockException,
    Exception\ORMException
};
use Doctrine\Persistence\{
    ManagerRegistry,
    Mapping\MappingException,
    ObjectManager,
    ObjectRepository
};

abstract class AbstractBaseRepository
{
    abstract protected static function entityClass(): string;

    protected ObjectRepository $objectRepository;

    /**
     * @throws NotSupported
     */
    public function __construct(
        protected readonly Connection $connection,
        private readonly ManagerRegistry $managerRegistry,
    ) {
        $this->objectRepository = $this->getEntityManager()->getRepository(self::entityClass());
    }

    /**
     * @return ObjectManager|EntityManager
     */
    public function getEntityManager(): EntityManager|ObjectManager
    {
        $entityManager = $this->managerRegistry->getManager();

        if ($entityManager->isOpen()) {
            return $entityManager;
        }

        return $this->managerRegistry->resetManager();
    }

    /**
     * @throws ORMException
     */
    protected function persistEntity(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MappingException
     */
    protected function flushData(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function saveEntity(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function removeEntity(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws Exception
     */
    protected function executeFetchQuery(string $query, array $params = []): array
    {
        return $this->connection->executeQuery($query, $params)->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    protected function executeQuery(string $query, array $params = []): void
    {
        $this->connection->executeQuery($query, $params);
    }
}
