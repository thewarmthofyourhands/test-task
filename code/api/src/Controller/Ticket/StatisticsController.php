<?php

declare(strict_types=1);

namespace App\Controller\Ticket;

use App\Enum\Infrastructure\Rest\ApplicationSuccessCodeEnum;
use App\Enum\Infrastructure\Rest\Messages\ApplicationSuccessMessageEnum;
use App\Infrastructure\Rest\ApiResponse;
use App\UseCase\Ticket\TicketStatisticsShowHandler;
use Eva\Http\Message\Request;
use Eva\Http\Message\Response;
use JsonException;

readonly class StatisticsController
{
    public function __construct(
        private TicketStatisticsShowHandler $ticketStatisticsShowHandler,
    ) {}

    /**
     * @throws JsonException
     */
    public function show(Request $request): Response
    {
        $responseData = $this->ticketStatisticsShowHandler->handle();

        return (new ApiResponse(
            $responseData,
            ApplicationSuccessCodeEnum::DEFAULT->value,
            ApplicationSuccessMessageEnum::DEFAULT->value,
        ))->build();
    }
}
