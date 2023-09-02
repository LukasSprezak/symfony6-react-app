<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusProductEnum: string
{
    case DRAFT = 'DRAFT';
    case PUBLISHED = 'PUBLISHED';
    case FINISHED = 'FINISHED';

    public function isStatusProduct(?string $status): bool
    {
        return $this->value === $status;
    }
}
