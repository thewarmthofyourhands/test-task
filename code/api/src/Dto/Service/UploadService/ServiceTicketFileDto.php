<?php

declare(strict_types=1);

namespace App\Dto\Service\UploadService;

final readonly class ServiceTicketFileDto
{
    public function __construct(
        private int $id,
        private int $ticketId,
        private string $path,
        private string $link,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTicketId(): int
    {
        return $this->ticketId;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
