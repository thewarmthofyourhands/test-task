<?php

declare(strict_types=1);

namespace App\Controller\Ticket;

use App\Dto\Service\UploadService\ServiceTicketFileDto;
use App\Dto\UseCase\Customer\AddCustomerDto;
use App\Dto\UseCase\Ticket\AddTicketDto;
use App\Dto\UseCase\Ticket\TicketDto;
use App\Enum\Application\Service\TicketService\TicketStatusEnum;
use App\Enum\Infrastructure\Rest\ApplicationSuccessCodeEnum;
use App\Enum\Infrastructure\Rest\Messages\ApplicationSuccessMessageEnum;
use App\Exception\Validation\ValidatorException;
use App\Infrastructure\Rest\ApiResponse;
use App\UseCase\Auth\GetAuthUserHandler;
use App\UseCase\Ticket\AddTicketHandler;
use App\UseCase\Ticket\ChangeTicketStatusHandler;
use App\UseCase\Ticket\GetTicketHandler;
use App\UseCase\Ticket\GetTicketListHandler;
use App\Validation\Validator;
use Eva\Http\Message\Request;
use Eva\Http\Message\Response;
use Eva\Http\Parser\JsonRequestParser;
use JsonException;
use Throwable;

readonly class TicketController
{
    public function __construct(
        private AddTicketHandler $addTicketHandler,
        private ChangeTicketStatusHandler $changeTicketStatusHandler,
        private GetTicketHandler $getTicketHandler,
        private GetTicketListHandler $getTicketListHandler,
        private GetAuthUserHandler $getAuthUserHandler,
        private Validator $validator,
    ) {}

    /**
     * @throws Throwable
     * @throws ValidatorException
     * @throws JsonException
     */
    public function store(Request $request): Response
    {
        $data = JsonRequestParser::parseBody($request);
        $this->validator->validate($data, '/Rest/TicketController/Store.json');
        $data = [...$data, 'customer' => new AddCustomerDto(...$data['customer'])];
        $id = $this->addTicketHandler->handle(new AddTicketDto(...$data));

        return (new ApiResponse(
            compact('id'),
            ApplicationSuccessCodeEnum::CREATED->value,
            ApplicationSuccessMessageEnum::CREATED->value,
        ))->build(201);
    }

    /**
     * @throws JsonException
     * @throws ValidatorException
     */
    public function patch(Request $request, string $id): Response
    {
        $id = (int) $id;
        $this->getAuthUserHandler->handle($request->getHeaders()['authorization'] ?? $request->getHeaders()['Authorization'] ?? '');
        $data = JsonRequestParser::parseBody($request);
        $this->validator->validate($data, '/Rest/TicketController/Patch.json');
        $this->changeTicketStatusHandler->handle($id, TicketStatusEnum::from($data['status']));

        return (new ApiResponse(
            null,
            ApplicationSuccessCodeEnum::DEFAULT->value,
            ApplicationSuccessMessageEnum::DEFAULT->value,
        ))->build();
    }

    public function show(Request $request, string $id): Response
    {
        $id = (int) $id;
        $this->getAuthUserHandler->handle($request->getHeaders()['authorization'] ?? $request->getHeaders()['Authorization'] ?? '');
        $ticketDto = $this->getTicketHandler->handle($id);
        $customerDto = $ticketDto->getCustomer();
        $attachments = array_map(static fn(ServiceTicketFileDto $fileDto): array => [
            'id' => $fileDto->getId(),
            'ticketId' => $fileDto->getTicketId(),
            'link' => $fileDto->getLink(),
            'createdAt' => $fileDto->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $fileDto->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], $ticketDto->getAttachments());
        $responseData = [
            'customer' => [
                'id' => $customerDto->getId(),
                'name' => $customerDto->getName(),
                'phone' => $customerDto->getPhone(),
                'email' => $customerDto->getEmail(),
                'createdAt' => $customerDto->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $customerDto->getUpdatedAt()->format('Y-m-d H:i:s'),
            ],
            'attachments' => $attachments,
            'id' => $ticketDto->getId(),
            'customerId' => $ticketDto->getCustomerId(),
            'topic' => $ticketDto->getTopic(),
            'text' => $ticketDto->getText(),
            'status' => $ticketDto->getStatus()->value,
            'managerReplyDate' => $ticketDto->getManagerReplyDate()?->format('Y-m-d H:i:s'),
            'createdAt' => $ticketDto->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $ticketDto->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        return (new ApiResponse(
            $responseData,
            ApplicationSuccessCodeEnum::DEFAULT->value,
            ApplicationSuccessMessageEnum::DEFAULT->value,
        ))->build();
    }

    public function index(Request $request): Response
    {
        $this->getAuthUserHandler->handle($request->getHeaders()['authorization'] ?? $request->getHeaders()['Authorization'] ?? '');
        $params = JsonRequestParser::parseParams($request);
        $filter = $params['filter'] ?? [];
        $ticketDtoList = $this->getTicketListHandler->handle($filter);
        $responseData = array_map(static function(TicketDto $ticketDto): array {
            $customerDto = $ticketDto->getCustomer();

            return [
                'customer' => [
                    'id' => $customerDto->getId(),
                    'name' => $customerDto->getName(),
                    'phone' => $customerDto->getPhone(),
                    'email' => $customerDto->getEmail(),
                    'createdAt' => $customerDto->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $customerDto->getUpdatedAt()->format('Y-m-d H:i:s'),
                ],
                'id' => $ticketDto->getId(),
                'customerId' => $ticketDto->getCustomerId(),
                'topic' => $ticketDto->getTopic(),
                'text' => $ticketDto->getText(),
                'status' => $ticketDto->getStatus()->value,
                'managerReplyDate' => $ticketDto->getManagerReplyDate()?->format('Y-m-d H:i:s'),
                'createdAt' => $ticketDto->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $ticketDto->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $ticketDtoList);

        return (new ApiResponse(
            $responseData,
            ApplicationSuccessCodeEnum::DEFAULT->value,
            ApplicationSuccessMessageEnum::DEFAULT->value,
        ))->build();
    }
}
