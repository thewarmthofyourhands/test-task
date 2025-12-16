<?php

declare(strict_types=1);

namespace App\Dto\Repository\UserRepository;

final readonly class UserDto
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
        private string $password,
        private string $token,
        private string $created_at,
        private string $updated_at,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }
}
