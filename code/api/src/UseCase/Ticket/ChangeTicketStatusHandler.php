<?php

declare(strict_types=1);

namespace App\UseCase\Ticket;

use App\Enum\Application\Service\TicketService\TicketStatusEnum;
use App\Service\TicketService;

readonly class ChangeTicketStatusHandler
{
    public function __construct(
        private TicketService $ticketService,
    ) {}

    public function handle(int $id, TicketStatusEnum $ticketStatusEnum): void
    {
        $this->ticketService->changeStatus($id, $ticketStatusEnum);
    }
}
