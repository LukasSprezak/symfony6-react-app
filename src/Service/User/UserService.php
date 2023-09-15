<?php

declare(strict_types=1);

namespace App\Service\User;

use App\{
    Core\Infrastructure\Queue\User\UserCreate\Query\UserCreateQuery,
    DTO\User\CreateUserDTO,
    Entity\User,
    Repository\UserRepository,
    Service\Password\PasswordService
};
use Symfony\Component\{
    HttpKernel\Exception\BadRequestHttpException,
    Messenger\MessageBusInterface,
    Security\Core\Exception\UserNotFoundException
};
use DateTimeImmutable;
use function sha1;
use function sprintf;
use function uniqid;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordService $passwordService,
        private MessageBusInterface $messageBus
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

        $this->userRepository->save($user);

        $this->messageBus->dispatch(
            new UserCreateQuery(
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getToken()
            )
        );

        return $user;
    }

    public function activateAccount(string $id, string $token): User
    {
        $user = $this->findOneInactiveUserByIdAndTokenOrFail($id, $token);

        $user->setEnabled(true);
        $user->setToken(null);

        $this->userRepository->save($user);

        return $user;
    }

    public function changePassword(string $userId, string $oldPassword, string $newPassword): User
    {
        $user = $this->findOneUserByIdOrFail($userId);

        if (!$this->passwordService->isValidPassword($user, $oldPassword)) {
            throw new BadRequestHttpException(message: 'Old password does not match.');
        }

        $user->setPassword($this->passwordService->generateEncodedPassword($user, $newPassword));
        $user->setLastPasswordChange(new DateTimeImmutable('now'));

        $this->userRepository->save($user);

        return $user;
    }

    public function resetPassword(string $email): User
    {
        $user = $this->findOneUserByEmailOrFail($email);
        $user->setResetPasswordToken(sha1(uniqid(prefix: '', more_entropy: true)));

        $this->userRepository->save($user);

        return $user;
    }

    private function findOneInactiveUserByIdAndTokenOrFail(string $id, string $token): User
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

    private function findOneUserByEmailOrFail(string $email): User
    {
        if (null === $user = $this->userRepository->findOneBy([
            'email' => $email
        ])) {
            throw new UserNotFoundException(sprintf('User with email %s not found', $email));
        }

        return $user;
    }

    private function findOneUserByIdOrFail(string $id): User
    {
        if (null === $user = $this->userRepository->find($id)) {
            throw new UserNotFoundException(sprintf('User with id %s not found', $id));
        }

        return $user;
    }
}
