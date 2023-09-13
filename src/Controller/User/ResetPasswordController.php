<?php

declare(strict_types=1);

namespace App\Controller\User;

use Symfony\Component\{
    HttpKernel\Attribute\AsController,
    HttpFoundation\JsonResponse,
    HttpFoundation\Request,
    HttpFoundation\Response
};
use App\Service\User\UserService;
use Exception;

#[AsController]
final readonly class ResetPasswordController
{
    public function __construct(private UserService $userService) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return new JsonResponse([
                'data' => [
                    'status' => true,
                    'resetPasswordData' => $this->userService->resetPassword($request->getPayload()->get(key: 'email')),
                ],
                'message' => 'Reset password email sent to you.',
                'code' => Response::HTTP_OK,
            ], status: Response::HTTP_OK);
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
