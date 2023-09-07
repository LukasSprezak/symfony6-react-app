<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusProductEnum: string
{
    case IN_PREPARATION = 'IN_PREPARATION';

    case IN_PROGRESS = 'IN_PROGRESS';

    case ORDER_CANCELLED = 'ORDER_CANCELLED';

    case COMPLETED = 'COMPLETED';

    case SENT_TO_CUSTOMER = 'SENT_TO_CUSTOMER';

    case RETRIEVED = 'RETRIEVED';

    case FINISHED = 'FINISHED';

    public function isStatusProduct(null|string $status): bool
    {
        return $this->value === $status;
    }
}
