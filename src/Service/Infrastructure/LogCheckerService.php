<?php

declare(strict_types=1);

namespace App\Service\Infrastructure;

use Symfony\{
    Bundle\SecurityBundle\Security,
    Component\HttpFoundation\RequestStack
};
use App\Entity\LogChecker;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class LogCheckerService
{
    private const ROUTE = '_route';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
    ) {}

    public function logger(
        string $entityType,
        int $entityId,
        string $action,
        array $eventData
    ): void {
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $log = new LogChecker();

        $log->setEntityType($entityType);
        $log->setEntityId($entityId);
        $log->setAction($action);
        $log->setEventData($eventData);
        $log->setUser($user);
        $log->setRequestRoute($request?->get(key: self::ROUTE));
        $log->setIpAddress($request?->getClientIp());
        $log->setCreatedAt(new DateTimeImmutable);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
