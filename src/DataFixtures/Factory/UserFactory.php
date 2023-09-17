<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use Symfony\Component\{
    Filesystem\Filesystem,
    PasswordHasher\Hasher\UserPasswordHasherInterface
};
use Zenstruck\Foundry\{
    Factory,
    ModelFactory
};
use App\{
    Entity\User,
    Enum\RoleEnum
};
use DateTimeImmutable;

use function Zenstruck\Foundry\faker;

class UserFactory extends ModelFactory
{
    private const MIME_PNG = '.png';

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    protected static function getClass(): string
    {
        return User::class;
    }

    public function promoteRole(string $role): self
    {
        $defaults = $this->getDefaults();
        $roles = [...$defaults['roles'], ...[$role]];

        return $this->addState([
            'roles' => $roles
        ]);
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email,
            'username' => faker()->userName(),
            'roles' => [
                RoleEnum::ROLE_USER->name
            ],
            'repeatPassword' => 'admin',
            'logo' => 'logo.png',
            'createdAt' => DateTimeImmutable::createFromMutable(faker()->dateTimeBetween('-2 year', '-1 year')),
            'enabled' => true,
        ];
    }

    public function initialize(): UserFactory|Factory
    {
        return $this
            ->afterInstantiate(function (User $user) {
                $passwordHash = $this->passwordHasher
                    ->hashPassword($user, $user->getPlainPassword())
                ;

                $user->setPassword($passwordHash);
                $user->setRepeatPassword($passwordHash);

                $filesystem = new Filesystem();
                $logoFileName = self::faker()->slug(nbWords: 3) . self::MIME_PNG;

                $file = __DIR__ . '/../../../client/images/' . $user->getLogo();
                $destination = __DIR__ . '/../../../public/uploads/logo/' . $logoFileName;

                $filesystem->copy(
                    $file,
                    $destination
                );

                $user->setLogo($logoFileName);
            });
    }
}
