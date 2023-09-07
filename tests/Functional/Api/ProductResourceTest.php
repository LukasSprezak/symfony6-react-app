<?php

declare(strict_types=1);

namespace Functional\Api;

use Functional\AbstractFunctionalTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductResourceTest extends AbstractFunctionalTest
{
    private const CREATE_PRODUCT = '/api/products';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateProduct(): void
    {
        $token = $this->login();
        $this->createClientWithCredentials($token)->request(Request::METHOD_POST, self::CREATE_PRODUCT, [
            'json' => [

            ]
        ]);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertJsonContains([
            'status' => true,
            'message' => 'Product create successful.'
        ]);
    }
}
