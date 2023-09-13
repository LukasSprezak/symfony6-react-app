<?php

declare(strict_types=1);

namespace App\Core\Queue\User\UserCreate\Handler;

use App\Core\Queue\User\UserCreate\Query\UserCreateQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreateHandler
{
    public function __invoke(UserCreateQuery $userCreateQuery)
    {
    }
}
