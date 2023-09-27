<?php

declare(strict_types=1);

namespace App\Core\Factory;

use Symfony\Component\{HttpFoundation\Request,
    HttpFoundation\Response,
    Serializer\Encoder\JsonEncoder,
    Serializer\SerializerInterface};

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

    /**
     * @throws \JsonException
     */
    public function transformJsonBody(Request $request): ?Request
    {
        $data = json_decode(
            json: $request->getContent(),
            associative: true,
            depth: JSON_THROW_ON_ERROR,
            flags: JSON_THROW_ON_ERROR
        );

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        if (null === $data) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
