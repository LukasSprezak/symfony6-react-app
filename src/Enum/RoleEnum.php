<?php

declare(strict_types=1);

namespace App\Enum;

enum RoleEnum: string
{
    case ROLE_USER = 'ROLE_USER';

    case ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    case ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    case ROLE_MERCHANT = 'ROLE_MERCHANT';

    case ROLE_ADMIN = 'ROLE_ADMIN';

    case ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
