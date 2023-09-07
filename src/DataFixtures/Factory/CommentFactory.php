<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Comment;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;

use function Zenstruck\Foundry\faker;

class CommentFactory extends ModelFactory
{
    protected static function getClass(): string
    {
        return Comment::class;
    }

    protected function getDefaults(): array
    {
        return  [
            'content' => self::faker()->sentence,
            'owner' => UserFactory::random(),
            'product' => ProductFactory::random(),
            'publishedAt' => DateTimeImmutable::createFromMutable(faker()->dateTimeThisYear),
        ];
    }
}
