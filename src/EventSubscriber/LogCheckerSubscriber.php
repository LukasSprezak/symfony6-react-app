<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Doctrine\{
    Bundle\DoctrineBundle\Attribute\AsDoctrineListener,
    ORM\EntityManagerInterface,
    ORM\Events,
    Persistence\Event\LifecycleEventArgs
};
use App\{
    Enum\DatabaseEnum,
    Service\Infrastructure\LogCheckerService,
    Entity\LogChecker
};
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;
use function str_replace;
use function get_class;
use function array_pop;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preRemove, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postRemove, priority: 500, connection: 'default')]
class LogCheckerSubscriber
{
    private const NAMESPACE = 'App\Entity\\';

    public function __construct(
        private readonly LogCheckerService $logCheckerService,
        private readonly SerializerInterface $serializer,
        private $removal = [],
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            'postPersist',
            'postUpdate',
            'preRemove',
            'postRemove',
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        $this->log($entityManager, $entity, DatabaseEnum::INSERT->value);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        $this->log($entityManager, $entity, DatabaseEnum::UPDATE->value);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->removal[] = $this->serializer->normalize($entity);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        $this->log($entityManager, $entity, DatabaseEnum::DELETE->value);
    }

    private function log(
        EntityManagerInterface $entityManager,
        object $entity,
        string $action,
    ): void {
        $entityClass = get_class($entity);
        Assert::object($entity);

        if ($entityClass === LogChecker::class) {
            return;
        }

        $entityId = $entity->getId();

        $entityType = str_replace(search: self::NAMESPACE, replace: '', subject: $entityClass);

        $uow = $entityManager->getUnitOfWork();

        if ($action === DatabaseEnum::DELETE->value) {
            $entityData = array_pop($this->removal);
            $entityId = $entityData['id'];

        } elseif ($action === DatabaseEnum::INSERT->value) {
            $entityData = $this->serializer->normalize($entity);

        } else {
            $entityData = $uow->getEntityChangeSet($entity);

            foreach ($entityData as $field => $change) {
                $entityData[$field] = [
                    'from' => $change[0],
                    'to' => $change[1],
                ];
            }
        }

        $this->logCheckerService->logger($entityType, $entityId, $action, $entityData);
    }
}
