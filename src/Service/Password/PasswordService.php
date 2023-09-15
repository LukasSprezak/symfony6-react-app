<?php

declare(strict_types=1);

namespace App\Service\Password;

use Symfony\Component\{
    HttpKernel\Exception\BadRequestHttpException
};
use Symfony\Component\{
    PasswordHasher\Hasher\UserPasswordHasherInterface,
    Security\Core\User\UserInterface
};
use App\Entity\User;
use function strlen;

final class PasswordService
{
    private const MINIMUM_LENGTH = 8;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function generateEncodedPassword(UserInterface $user, string $password): string
    {
        if (self::MINIMUM_LENGTH > strlen($password)) {
            throw new BadRequestHttpException(message: 'Password must be at least 8 characters.');
        }

        return $this->passwordHasher->hashPassword($user, $password);
    }

    public function isValidPassword(User $user, string $oldPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $oldPassword);
    }
}
