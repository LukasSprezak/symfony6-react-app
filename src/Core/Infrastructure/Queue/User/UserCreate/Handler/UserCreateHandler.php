<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Queue\User\UserCreate\Handler;

use App\Core\Infrastructure\Queue\User\UserCreate\Query\UserCreateQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UserCreateHandler
{
    public function __invoke(UserCreateQuery $userCreateQuery) {}
}
