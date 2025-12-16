<?php

declare(strict_types=1);

namespace App\UseCase\Ticket;

use App\Service\TicketService;

readonly class TicketStatisticsShowHandler
{
    public function __construct(
        private TicketService $ticketService,
    ) {}

    public function handle(): array
    {
        return $this->ticketService->getStatistics();
    }
}
