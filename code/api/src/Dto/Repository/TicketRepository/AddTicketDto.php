<?php

declare(strict_types=1);

namespace App\Dto\Repository\TicketRepository;

final readonly class AddTicketDto
{
    public function __construct(
        private int $customerId,
        private string $topic,
        private string $text,
        private string $status,
        private null|string $managerReplyDate,
    ) {}

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
}
