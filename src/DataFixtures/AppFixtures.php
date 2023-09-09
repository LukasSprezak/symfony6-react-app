<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Factory\{
    CategoryFactory,
    CommentFactory,
    ProductFactory,
    TagFactory,
    UserFactory
};
use Doctrine\{
    Bundle\FixturesBundle\Fixture,
    Persistence\ObjectManager
};
use App\Enum\RoleEnum;
use Exception;

use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()
            ->withAttributes([
                'email' => 'admin@admin.com',
                'plainPassword' => 'admin'
            ])
            ->promoteRole(RoleEnum::ROLE_ADMIN->value)
            ->create();

        UserFactory::new()
            ->withAttributes([
                'email' => 'user@admin.com',
                'plainPassword' => 'admin'
            ])
            ->promoteRole(RoleEnum::ROLE_USER->value)
            ->create();

        ProductFactory::new()
            ->many(20)
            ->create(function() {
                return [
                    'tags' => TagFactory::createMany(20),
                    'category' => CategoryFactory::new([
                        'name' => faker()->words(random_int(2,6), asText: true)
                        ]),
                    ];
                })
            ;

        CommentFactory::new()->createMany(30);

        $manager->flush();
    }
}
