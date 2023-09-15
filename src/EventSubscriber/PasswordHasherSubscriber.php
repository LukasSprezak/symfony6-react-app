<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\{
    EventDispatcher\EventSubscriberInterface,
    HttpFoundation\Request,
    HttpKernel\Event\ViewEvent,
    HttpKernel\KernelEvents,
    PasswordHasher\Hasher\UserPasswordHasherInterface
};
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;

final readonly class PasswordHasherSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                'hashPassword', EventPriorities::PRE_WRITE
            ]
        ];
    }

    public function hashPassword(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT], strict: true)) {
            return;
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $user->setRepeatPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getRepeatPassword()
            )
        );
    }
}
