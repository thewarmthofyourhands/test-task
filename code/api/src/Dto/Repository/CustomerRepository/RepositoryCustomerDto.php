<?php

declare(strict_types=1);

namespace App\Dto\Repository\CustomerRepository;

final readonly class RepositoryCustomerDto
{
    public function __construct(
        private int $id,
        private string $name,
        private string $phone,
        private string $email,
        private string $createdAt,
        private string $updatedAt,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
