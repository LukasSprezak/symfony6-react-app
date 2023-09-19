<?php

declare(strict_types=1);

namespace App\Service\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class ServiceException extends HttpException
{
    public function __construct(private readonly ExceptionDataQuery $exceptionData)
    {
        $statusCode = $exceptionData->getStatusCode();
        $message = $exceptionData->getMessage();

        parent::__construct($statusCode, $message);
    }

    public function getExceptionData(): ExceptionDataQuery
    {
        return $this->exceptionData;
    }
}
