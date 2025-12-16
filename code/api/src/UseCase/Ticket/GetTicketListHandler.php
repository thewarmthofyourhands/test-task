<?php

declare(strict_types=1);

namespace App\UseCase\Ticket;

use App\Dto\Service\CustomerService\ServiceCustomerDto;
use App\Dto\Service\TicketService\ServiceTicketDto;
use App\Dto\UseCase\Customer\CustomerDto;
use App\Dto\UseCase\Ticket\TicketDto;
use App\Service\CustomerService;
use App\Service\TicketService;
use DateMalformedStringException;

readonly class GetTicketListHandler
{
    public function __construct(
        private TicketService $ticketService,
        private CustomerService $customerService,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public function handle(array $filter): array
    {
        $serviceTicketDtoList = $this->ticketService->getAll($filter);
        $customerIdList = array_map(
            static fn(ServiceTicketDto $dto): int => $dto->getCustomerId(),
            $serviceTicketDtoList,
        );
        $serviceCustomerDtoList = $this->customerService->getByIdList($customerIdList);
        $customerDtoList =  array_map(
            static fn(ServiceCustomerDto $serviceCustomerDto): CustomerDto => new CustomerDto(
                $serviceCustomerDto->getId(),
                $serviceCustomerDto->getName(),
                $serviceCustomerDto->getPhone(),
                $serviceCustomerDto->getEmail(),
                $serviceCustomerDto->getCreatedAt(),
                $serviceCustomerDto->getUpdatedAt(),
            ),
            $serviceCustomerDtoList,
        );

        return array_map(
            static function(ServiceTicketDto $serviceTicketDto) use ($customerDtoList): TicketDto {
                return new TicketDto(
                    $customerDtoList[$serviceTicketDto->getCustomerId()],
                    [],
                    $serviceTicketDto->getId(),
                    $serviceTicketDto->getCustomerId(),
                    $serviceTicketDto->getTopic(),
                    $serviceTicketDto->getText(),
                    $serviceTicketDto->getStatus(),
                    $serviceTicketDto->getManagerReplyDate(),
                    $serviceTicketDto->getCreatedAt(),
                    $serviceTicketDto->getUpdatedAt(),
                );
            },
            $serviceTicketDtoList,
        );
    }
}
