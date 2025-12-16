<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Service\CustomerService\AddCustomerDto;
use App\Dto\Repository\CustomerRepository\AddCustomerDto as RepositoryAddCustomerDto;
use App\Dto\Service\CustomerService\ServiceCustomerDto;
use App\Enum\Infrastructure\Rest\ApplicationErrorCodeEnum;
use App\Exception\Application\ApplicationException;
use App\Exception\Application\NotFoundException;
use App\Repository\CustomerRepository;
use DateMalformedStringException;
use DateTimeImmutable;

readonly class CustomerService
{
    public function __construct(private CustomerRepository $repository) {}

    public function addCustomer(AddCustomerDto $addCustomerDto): int
    {
        return $this->repository->addCustomer((new RepositoryAddCustomerDto(
            $addCustomerDto->getName(),
            $addCustomerDto->getPhone(),
            $addCustomerDto->getEmail(),
        )));
    }

    public function getById(int $id): ServiceCustomerDto
    {
        $repositoryDto = $this->repository->findById($id);

        if (null === $repositoryDto) {
            throw new NotFoundException();
        }

        return new ServiceCustomerDto(
            $repositoryDto->getId(),
            $repositoryDto->getName(),
            $repositoryDto->getPhone(),
            $repositoryDto->getEmail(),
            new DateTimeImmutable($repositoryDto->getCreatedAt()),
            new DateTimeImmutable($repositoryDto->getUpdatedAt()),
        );
    }

    /**
     * @param int[] $idList
     *
     * @return ServiceCustomerDto[]
     *
     * @throws DateMalformedStringException
     */
    public function getByIdList(array $idList): array
    {
        $repositoryDtoList = $this->repository->getByIdList($idList);

        return array_map(
            static fn($repositoryDto): ServiceCustomerDto => new ServiceCustomerDto(
                $repositoryDto->getId(),
                $repositoryDto->getName(),
                $repositoryDto->getPhone(),
                $repositoryDto->getEmail(),
                new DateTimeImmutable($repositoryDto->getCreatedAt()),
                new DateTimeImmutable($repositoryDto->getUpdatedAt()),
            ),
            $repositoryDtoList,
        );
    }

    public function assertCustomerHasNoTickets(string $email, string $phone): void
    {
        $isHas = $this->repository->isCustomerHasTicketsForDay($email, $phone);

        if (true === $isHas) {
            throw new ApplicationException(ApplicationErrorCodeEnum::SUBMIT_LIMIT);
        }
    }
}
