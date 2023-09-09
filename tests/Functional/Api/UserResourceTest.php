<?php

declare(strict_types=1);

namespace Functional\Api;

use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Contracts\HttpClient\Exception\{ClientExceptionInterface,
    DecodingExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface};
use Functional\AbstractFunctionalTest;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends AbstractFunctionalTest
{
    use ReloadDatabaseTrait;

    private const USERS_URI = '/api/users';

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function testGetCollectionUsers(): void
    {
        $token = $this->login();
        $response = $this->createClientWithCredentials($token)->request(Request::METHOD_GET, self::USERS_URI);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
        ]);

        self::assertCount(expectedCount: 2, haystack: $response->toArray()['hydra:member']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateUser(): void
    {
        $token = $this->login();
        $this->createClientWithCredentials($token)->request(Request::METHOD_POST, self::USERS_URI, [
            'json' => [
                '@context' => '/api/contexts/User',
                '@id' => '/api/users/3',
                '@type' => 'User',
                'username' => 'username2',
                'email' => 'user2@admin.com',
                'password' => 'admin',
                'repeatPassword' => 'admin',
                'enabled' => true
            ]
        ]);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_CREATED);
    }
}
