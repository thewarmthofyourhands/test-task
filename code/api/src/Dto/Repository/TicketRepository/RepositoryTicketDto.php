<?php

declare(strict_types=1);

namespace App\Dto\Repository\TicketRepository;

final readonly class RepositoryTicketDto
{
    public function __construct(
        private int $id,
        private int $customerId,
        private string $topic,
        private string $text,
        private string $status,
        private null|string $managerReplyDate,
        private string $createdAt,
        private string $updatedAt,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getManagerReplyDate(): null|string
    {
        return $this->managerReplyDate;
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
