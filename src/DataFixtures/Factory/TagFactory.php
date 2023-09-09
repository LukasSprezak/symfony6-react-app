<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Tag;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;

use function Zenstruck\Foundry\faker;

class TagFactory extends ModelFactory
{
    protected static function getClass(): string
    {
        return Tag::class;
    }

    protected function getDefaults(): array
    {
       return  [
           'name' => self::faker()->word,
           'createdAt' => DateTimeImmutable::createFromMutable(faker()->dateTimeBetween('-2 year', '-1 year')),
       ];
    }
}
