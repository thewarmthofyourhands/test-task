<?php

declare(strict_types=1);

namespace App\Dto\UseCase\Ticket;

final readonly class TicketDto
{
    public function __construct(
        private \App\Dto\UseCase\Customer\CustomerDto $customer,
        private array $attachments,
        private int $id,
        private int $customerId,
        private string $topic,
        private string $text,
        private \App\Enum\Application\Service\TicketService\TicketStatusEnum $status,
        private null|\DateTimeImmutable $managerReplyDate,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}

    public function getCustomer(): \App\Dto\UseCase\Customer\CustomerDto
    {
        return $this->customer;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

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

    public function getStatus(): \App\Enum\Application\Service\TicketService\TicketStatusEnum
    {
        return $this->status;
    }

    public function getManagerReplyDate(): null|\DateTimeImmutable
    {
        return $this->managerReplyDate;
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
