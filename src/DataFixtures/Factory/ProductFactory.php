<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Product;
use App\Enum\StatusProductEnum;
use Zenstruck\Foundry\ModelFactory;
use function Zenstruck\Foundry\faker;

class ProductFactory extends ModelFactory
{
    private const STATUS = [
        StatusProductEnum::PUBLISHED,
        StatusProductEnum::DRAFT,
        StatusProductEnum::FINISHED
    ];

    protected static function getClass(): string
    {
        return Product::class;
    }

    protected function getDefaults(): array
    {
        $randomStatus = self::STATUS[array_rand(self::STATUS)];

        return  [
            'name' => self::faker()->sentence,
            'owner' => UserFactory::random(),
            'createdAt' => faker()->dateTimeBetween('-2 year', '-1 year'),
            'updatedAt' => faker()->dateTimeThisYear,
            'status' => $randomStatus,
            'description' => faker()->text(maxNbChars: 100),
            'slug' => faker()->slug()
        ];
    }
}