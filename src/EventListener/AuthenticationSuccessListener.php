<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

final readonly class AuthenticationSuccessListener
{
    public function __construct(private IriConverterInterface $iriConverter) {}

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['id'] = $this->iriConverter->getIriFromResource($user);

        $event->setData($data);
    }
}
