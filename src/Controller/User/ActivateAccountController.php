<?php

declare(strict_types=1);

namespace App\Controller\User;

use Symfony\Component\{HttpFoundation\JsonResponse,
    HttpFoundation\Request,
    HttpFoundation\Response,
    HttpKernel\Attribute\AsController
};
use App\Service\User\UserService;
use Exception;

#[AsController]
readonly class ActivateAccountController
{
    public function __construct(private UserService $userService) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        try {
            return new JsonResponse([
                'data' => [
                    'status' => true,
                    'userData' => $this->userService->activateAccount($id, $request->getPayload()->get(key: 'token')),
                ],
                'message' => 'User has been successfully activated',
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
}
