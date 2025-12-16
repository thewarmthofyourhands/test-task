<?php

declare(strict_types=1);

namespace App\UseCase\Ticket;

use App\Dto\UseCase\Customer\CustomerDto;
use App\Dto\UseCase\Ticket\TicketDto;
use App\Service\CustomerService;
use App\Service\TicketService;
use App\Service\UploadService;

readonly class GetTicketHandler
{
    public function __construct(
        private TicketService $ticketService,
        private CustomerService $customerService,
        private UploadService $uploadService,
    ) {}

    public function handle(int $id): TicketDto
    {
        $serviceTicketDto = $this->ticketService->getById($id);
        $serviceCustomerDto = $this->customerService->getById($serviceTicketDto->getCustomerId());
        $customerDto = new CustomerDto(
            $serviceCustomerDto->getId(),
            $serviceCustomerDto->getName(),
            $serviceCustomerDto->getPhone(),
            $serviceCustomerDto->getEmail(),
            $serviceCustomerDto->getCreatedAt(),
            $serviceCustomerDto->getUpdatedAt(),
        );
        $attachments = $this->uploadService->getAttachmentsByTicketId($serviceTicketDto->getId());

        return new TicketDto(
            $customerDto,
            $attachments,
            $serviceTicketDto->getId(),
            $serviceTicketDto->getCustomerId(),
            $serviceTicketDto->getTopic(),
            $serviceTicketDto->getText(),
            $serviceTicketDto->getStatus(),
            $serviceTicketDto->getManagerReplyDate(),
            $serviceTicketDto->getCreatedAt(),
            $serviceTicketDto->getUpdatedAt(),
        );
    }
}
