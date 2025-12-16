<?php

declare(strict_types=1);

namespace App\Dto\UseCase\Ticket;

final readonly class AddTicketDto
{
    public function __construct(
        private \App\Dto\UseCase\Customer\AddCustomerDto $customer,
        private string $topic,
        private string $text,
        private array $attachments,
    ) {}

    public function getCustomer(): \App\Dto\UseCase\Customer\AddCustomerDto
    {
        return $this->customer;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
