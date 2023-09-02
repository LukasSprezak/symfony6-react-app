<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Factory\CategoryFactory;
use App\DataFixtures\Factory\CommentFactory;
use App\DataFixtures\Factory\ProductFactory;
use App\DataFixtures\Factory\TagFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Enum\RoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
                'email' => 'admin@admin.pl',
                'plainPassword' => 'admin'
            ])
            ->promoteRole(RoleEnum::ROLE_ADMIN->name)
            ->create();

        UserFactory::new()
            ->withAttributes([
                'email' => 'user@admin.pl',
                'plainPassword' => 'admin'
            ])
            ->promoteRole(RoleEnum::ROLE_USER->name)
            ->create();

        ProductFactory::new()
            ->many(20)
            ->create(function() {
                return [
                    'tags' => TagFactory::createMany(20),
                    'category' => CategoryFactory::new([
                        'name' => faker()->words(random_int(2,6), true)
                        ]),
                    ];
                })
            ;

        CommentFactory::new()->createMany(30);

        $manager->flush();
    }
}
