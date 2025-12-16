<?php

declare(strict_types=1);

namespace App\Dto\Repository\TicketFileRepository;

final readonly class RepositoryTicketFileDto
{
    public function __construct(
        private int $id,
        private int $ticketId,
        private string $name,
        private string $path,
        private string $createdAt,
        private string $updatedAt,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTicketId(): int
    {
        return $this->ticketId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
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
