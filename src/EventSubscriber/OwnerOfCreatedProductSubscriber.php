<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\{
    EventDispatcher\EventSubscriberInterface,
    HttpFoundation\Request,
    HttpKernel\Event\ViewEvent,
    HttpKernel\KernelEvents,
    Security\Core\Authentication\Token\Storage\TokenStorageInterface
};
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\OwnerInterface;

final readonly class OwnerOfCreatedProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                'getAuthUser',
                EventPriorities::PRE_WRITE
            ]
        ];
    }

    public function getAuthUser(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        $owner = $this->tokenStorage->getToken()?->getUser();

        if ((!$entity instanceof OwnerInterface) || Request::METHOD_POST !== $method) {
            return;
        }

        $entity->setOwner($owner);
    }
}
