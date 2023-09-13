<?php

declare(strict_types=1);

namespace App\Core\Queue\User\UserCreate\Query;

class UserCreateQuery
{
    private int $id;
    private string $username;
    private string $email;
    private string $token;

    public function __construct(int $id, string $username, string $email, string $token)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->token = $token;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
