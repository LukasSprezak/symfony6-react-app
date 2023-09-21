<?php

declare(strict_types=1);

namespace App\Core\Factory;

use Symfony\Component\{
    HttpFoundation\Response,
    Serializer\Encoder\JsonEncoder,
    Serializer\SerializerInterface
};

final readonly class JsonResponseFactory
{
    public function __construct(private SerializerInterface $serializer) {}

    public function create(object $data, int $status = Response::HTTP_OK, array $headers = []): Response
    {
        return new Response($this->serializer->serialize($data, JsonEncoder::FORMAT),
            $status,
            array_merge($headers, [
                'Content-Type' => 'application/json;charset=UTF-8'
            ])
        );
    }
}
