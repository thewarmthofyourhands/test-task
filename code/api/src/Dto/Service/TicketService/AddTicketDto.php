<?php

declare(strict_types=1);

namespace App\Dto\Service\TicketService;

final readonly class AddTicketDto
{
    public function __construct(
        private int $customerId,
        private string $topic,
        private string $text,
        private \App\Enum\Application\Service\TicketService\TicketStatusEnum $status,
        private null|\DateTimeImmutable $managerReplyDate,
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

    public function getStatus(): \App\Enum\Application\Service\TicketService\TicketStatusEnum
    {
        return $this->status;
    }

    public function getManagerReplyDate(): null|\DateTimeImmutable
    {
        return $this->managerReplyDate;
    }
}
