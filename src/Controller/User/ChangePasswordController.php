<?php

declare(strict_types=1);

namespace App\Controller\User;

use Symfony\Component\{
    HttpFoundation\JsonResponse,
    HttpFoundation\Request,
    HttpFoundation\Response,
    HttpKernel\Attribute\AsController
};
use App\Service\User\UserService;
use Exception;

#[AsController]
final readonly class ChangePasswordController
{
    public function __construct(private UserService $userService) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        try {
            return new JsonResponse([
                'data' => [
                    'status' => true,
                    'changePassword' => $this->userService->changePassword(
                        $id,
                        $request->getPayload()->get(key: 'oldPassword'),
                        $request->getPayload()->get(key: 'newPassword')
                    )
                ],
                'message' => 'The password has been successfully changed.',
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
