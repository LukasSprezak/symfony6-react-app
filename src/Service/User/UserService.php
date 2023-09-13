<?php

declare(strict_types=1);

namespace App\Service\User;

use App\{DTO\User\CreateUserDTO, Repository\UserRepository, Entity\User, Service\Password\PasswordService};
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use function sha1;
use function uniqid;
use function sprintf;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordService $passwordService,
    ) {}

    public function createAccount(CreateUserDTO $createUserDTO): User
    {
        $user = new User();

        $user->setUsername($createUserDTO->getUsername());
        $user->setEmail($createUserDTO->getEmail());
        $user->setPassword($this->passwordService->generateEncodedPassword($user, $createUserDTO->getPassword()));
        $user->setRepeatPassword($this->passwordService->generateEncodedPassword($user, $createUserDTO->getRepeatPassword()));
        $user->setToken(sha1(uniqid(prefix: '', more_entropy: true)));
        $user->setEnabled(false);

        return $this->userRepository->save($user);
    }

    public function activateAccount(string $id, string $token): User
    {
        $user = $this->findOneInactiveByIdAndTokenOrFail($id, $token);

        $user->setEnabled(true);
        $user->setToken(null);

        $this->userRepository->save($user);

        return $user;
    }

    private function findOneInactiveByIdAndTokenOrFail(string $id, string $token): User
    {
        $user = $this->userRepository->findOneBy([
            'id' => $id,
            'token' => $token,
            'enabled' => false,
        ]);

        if (null === $user) {
            throw new UserNotFoundException(sprintf('User with id %s and token %s not found', $id, $token));
        }

        return $user;
    }
}
