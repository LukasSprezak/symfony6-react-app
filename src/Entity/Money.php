<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\{
    DBAL\Types\Types,
    ORM\Mapping\Column,
    ORM\Mapping\Embeddable
};

#[Embeddable]
class Money
{
    #[Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private float $amount;

    #[Column(type: Types::STRING)]
    private string $currency;

    public function __construct(float $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
