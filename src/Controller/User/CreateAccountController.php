<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\{
    DTO\User\CreateUserDTO,
    Entity\User,
    Service\User\UserService
};
use Symfony\Component\{
    HttpFoundation\JsonResponse,
    HttpFoundation\Response,
    HttpKernel\Attribute\AsController
};
use Exception;

#[AsController]
readonly class CreateAccountController
{
    public function __construct(private UserService $userService) {}

    public function __invoke(CreateUserDTO $createUserDTO): JsonResponse
    {
        try {
            $user = $this->userService->createAccount($createUserDTO);

            return new JsonResponse([
                'data' => [
                    'status' => true,
                    'userData' => $this->toDto($user),
                ],
                'message' => 'User created successfully.',
                'code' => Response::HTTP_CREATED,
            ], status: Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return new JsonResponse([
                'data' => [
                    'status' => false,
                ],
                'message' => $exception->getMessage(),
                'code' => Response::HTTP_BAD_REQUEST,
            ], status: Response::HTTP_BAD_REQUEST);
        }
    }

    private function toDto(User $createUserDTO): CreateUserDTO
    {
        return CreateUserDTO::from(
            $createUserDTO->getUsername(),
            $createUserDTO->getEmail(),
            $createUserDTO->getPassword(),
            $createUserDTO->getRepeatPassword(),
            $createUserDTO->getEnabled(),
        );
    }
}
