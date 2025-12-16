<?php

declare(strict_types=1);

namespace App\UseCase\Ticket;

use App\Dto\Service\CustomerService\AddCustomerDto;
use App\Dto\Service\TicketService\AddTicketDto as ServiceAddTicketDto;
use App\Dto\UseCase\Ticket\AddTicketDto;
use App\Enum\Application\Service\TicketService\TicketStatusEnum;
use App\Service\CustomerService;
use App\Service\TicketService;
use App\Service\UploadService;
use Eva\Database\ConnectionStoreInterface;
use Throwable;

readonly class AddTicketHandler
{
    public function __construct(
        private ConnectionStoreInterface $connectionStore,
        private UploadService $uploadService,
        private TicketService $ticketService,
        private CustomerService $customerService,
    ) {}

    public function handle(AddTicketDto $addTicketDto): int
    {
        $connection = $this->connectionStore->get();
        $addCustomerDto = $addTicketDto->getCustomer();
        $connection->beginTransaction();

        try {
            $this->customerService->assertCustomerHasNoTickets(
                $addCustomerDto->getName(),
                $addCustomerDto->getPhone(),
            );
            $customerId = $this->customerService->addCustomer((new AddCustomerDto(
                $addCustomerDto->getName(),
                $addCustomerDto->getPhone(),
                $addCustomerDto->getEmail(),
            )));
            $id = $this->ticketService->addTicket((new ServiceAddTicketDto(
                $customerId,
                $addTicketDto->getTopic(),
                $addTicketDto->getText(),
                TicketStatusEnum::NEW,
                null,
            )));
            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollBack();
            throw $e;
        }

        //@TODO Вынести в асинхронное исполнение
        foreach ($addTicketDto->getAttachments() as $key) {
            $this->uploadService->finalizeUploadedFile($key, $id);
        }

        return $id;
    }
}
