<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\Exception\{
    ExceptionDataQuery,
    ServiceException
};
use Symfony\Component\{HttpFoundation\JsonResponse,
    HttpFoundation\Response,
    HttpKernel\Event\ExceptionEvent,
};

final readonly class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ServiceException) {
            $exceptionData = $exception->getExceptionData();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $exceptionData = new ExceptionDataQuery($statusCode, $exception->getMessage());
        }

        $response = new JsonResponse($exceptionData->toArray());
        $event->setResponse($response);
    }
}
