<?php

declare(strict_types=1);

namespace App\Service\Exception;

final readonly class ExceptionDataQuery
{
    public function __construct(protected int $statusCode, protected string $message) {}

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'statusCode' => $this->statusCode,
        ];
    }
}
