<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Repository\TicketRepository\RepositoryTicketDto;
use App\Dto\Service\TicketService\AddTicketDto;
use App\Dto\Repository\TicketRepository\AddTicketDto as RepositoryAddTicketDto;
use App\Dto\Service\TicketService\ServiceTicketDto;
use App\Enum\Application\Service\TicketService\TicketStatusEnum;
use App\Exception\Application\NotFoundException;
use App\Repository\TicketRepository;
use DateMalformedStringException;
use DateTimeImmutable;

readonly class TicketService
{
    public function __construct(private TicketRepository $repository) {}

    public function addTicket(AddTicketDto $addTicketDto): int
    {
        return $this->repository->addTicket((new RepositoryAddTicketDto(
            $addTicketDto->getCustomerId(),
            $addTicketDto->getTopic(),
            $addTicketDto->getText(),
            $addTicketDto->getStatus()->value,
            $addTicketDto->getManagerReplyDate()?->format('Y-m-d H:i:s'),
        )));
    }

    public function changeStatus(int $id, TicketStatusEnum $ticketStatusEnum): void
    {
        $this->repository->changeStatus($id, $ticketStatusEnum->value);
    }

    /**
     * @throws DateMalformedStringException
     * @throws NotFoundException
     */
    public function getById(int $id): ServiceTicketDto
    {
        $repositoryTicketDto = $this->repository->findById($id);

        if (null === $repositoryTicketDto) {
            throw new NotFoundException();
        }

        return new ServiceTicketDto(
            $repositoryTicketDto->getId(),
            $repositoryTicketDto->getCustomerId(),
            $repositoryTicketDto->getTopic(),
            $repositoryTicketDto->getText(),
            TicketStatusEnum::fromName($repositoryTicketDto->getStatus()),
            null === $repositoryTicketDto->getManagerReplyDate() ?
                null :
                new DateTimeImmutable($repositoryTicketDto->getManagerReplyDate()),
            new DateTimeImmutable($repositoryTicketDto->getCreatedAt()),
            new DateTimeImmutable($repositoryTicketDto->getUpdatedAt()),
        );
    }

    /**
     * @return ServiceTicketDto[]
     *
     * @throws DateMalformedStringException
     */
    public function getAll(array $filter): array
    {
        $repositoryTicketDtoList = $this->repository->getAll($filter);

        return array_map(
            static fn(RepositoryTicketDto $repositoryTicketDto): ServiceTicketDto => new ServiceTicketDto(
                $repositoryTicketDto->getId(),
                $repositoryTicketDto->getCustomerId(),
                $repositoryTicketDto->getTopic(),
                $repositoryTicketDto->getText(),
                TicketStatusEnum::fromName($repositoryTicketDto->getStatus()),
                null === $repositoryTicketDto->getManagerReplyDate() ?
                    null :
                    new DateTimeImmutable($repositoryTicketDto->getManagerReplyDate()),
                new DateTimeImmutable($repositoryTicketDto->getCreatedAt()),
                new DateTimeImmutable($repositoryTicketDto->getUpdatedAt()),
            ),
            $repositoryTicketDtoList,
        );
    }

    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }
}
