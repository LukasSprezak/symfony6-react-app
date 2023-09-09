<?php

declare(strict_types=1);

namespace Functional;

use ApiPlatform\Symfony\{
    Bundle\Test\Client,
    Bundle\Test\ApiTestCase
};
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
use Exception;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractFunctionalTest extends ApiTestCase
{
    private const LOGIN_CHECK_URI = '/api/login_check';

    protected EntityManagerInterface $entityManager;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::createClient()->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->clear();
        $this->entityManager->close();
    }

    protected function createClientWithCredentials($token = null): Client
    {
        return static::createClient([], ['headers' => ['Authorization' => 'Bearer ' . $token]]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function login(): string
    {
        $response = static::createClient()->request(Request::METHOD_POST, self::LOGIN_CHECK_URI, [
            'json' => [
                'username' => 'admin@admin.com',
                'password' => 'admin',
            ]
        ]);

        try {
            return $response->toArray()['token'];
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            $exception->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    protected function createUser(string $email, string $password): User
    {
        $user = new User();

        $user->setEmail($email);
        $user->setPassword(
            self::getContainer()->get('security.user_password_hasher')->hashPassword($user, $password)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function getAuthorizationUser(Client $client, string $userLogin, string $password): void
    {
        $client->request(Request::METHOD_POST, self::LOGIN_CHECK_URI, [
            'json' => [
                'username' => $userLogin,
                'password' => $password,
            ]
        ]);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    protected function createUserAndAuthorizationUser(Client $client, string $email, string $password): User
    {
        $user = $this->createUser($email, $password);
        $this->getAuthorizationUser($client, $email, $password);

        return $user;
    }
}
