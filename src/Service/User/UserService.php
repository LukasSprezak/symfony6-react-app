<?php

declare(strict_types=1);

namespace App\Service\User;

use App\{DTO\User\CreateUserDTO,
    Exception\Password\PasswordException,
    Messenger\Message\RequestResetPasswordMessage,
    Messenger\RoutingKey,
    Repository\UserRepository,
    Entity\User,
    Service\Password\PasswordService};
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
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
