<?php

declare(strict_types=1);

namespace App\Enum;

enum DatabaseEnum: string
{
    case INSERT = 'insert';

    case UPDATE = 'update';

    case DELETE = 'delete';
}
