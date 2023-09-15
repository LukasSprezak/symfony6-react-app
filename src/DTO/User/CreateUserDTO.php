<?php

declare(strict_types=1);

namespace App\DTO\User;

use JsonSerializable;

final class CreateUserDTO implements JsonSerializable
{
    private string $username;

    private string $email;

    private ?string $password;

    private string $repeatPassword;

    private bool $enabled;

    private function __construct(
        string $username,
        string $email,
        string $password,
        string $repeatPassword,
        bool $enabled,
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->repeatPassword = $repeatPassword;
        $this->enabled = $enabled;
    }

    public static function from(
        string $username,
        string $email,
        string $password,
        string $repeatPassword,
        bool $enabled,
    ): CreateUserDTO {
        return new self($username, $email, $password, $repeatPassword, $enabled);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRepeatPassword(): string
    {
        return $this->repeatPassword;
    }

    public function jsonSerialize(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'repeatPassword' => $this->repeatPassword,
            'enabled' => $this->enabled,
        ];
    }
}
