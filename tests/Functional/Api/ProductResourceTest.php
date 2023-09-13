<?php

declare(strict_types=1);

namespace Functional\Api;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    DecodingExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};
use Functional\AbstractFunctionalTest;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ProductResourceTest extends AbstractFunctionalTest
{
    use ReloadDatabaseTrait;

    private const CREATE_PRODUCT_URI = '/api/products';

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
        $this->createClientWithCredentials($token)->request(Request::METHOD_POST, self::CREATE_PRODUCT_URI, [
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
