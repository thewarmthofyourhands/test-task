<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Repository\CustomerRepository\AddCustomerDto;
use App\Dto\Repository\CustomerRepository\RepositoryCustomerDto;
use Eva\Database\ConnectionStoreInterface;
use PDO;

readonly class CustomerRepository
{
    public function __construct(private ConnectionStoreInterface $connectionStore)
    {
    }

    public function addCustomer(AddCustomerDto $addCustomerDto): int
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(
            <<<SQL
            insert into customers (name, email, phone)
            values (:name, :email, :phone)
            SQL,
            [
                'name' => $addCustomerDto->getName(),
                'email' => $addCustomerDto->getEmail(),
                'phone' => $addCustomerDto->getPhone(),
            ],
        );
        $stmt->execute();
        $stmt->closeCursor();

        return (int) $connection->lastInsertId();
    }

    public function findById(int $id): null|RepositoryCustomerDto
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(<<<SQL
            select * from customers where id = :id;
            SQL,
            ['id' => $id],
        );
        $stmt->execute();
        $dto = null;

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dto = new RepositoryCustomerDto(
                $el['id'],
                $el['name'],
                $el['phone'],
                $el['email'],
                $el['created_at'],
                $el['updated_at'],
            );
        }

        $stmt->closeCursor();

        return $dto;
    }

    /**
     * @param int[] $idList
     *
     * @return RepositoryCustomerDto[]
     */
    public function getByIdList(array $idList): array
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(<<<SQL
            select * from customers where id in (:idList);
            SQL,
            ['idList' => $idList ?: null],
        );
        $stmt->execute();
        $dtoList = [];

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dtoList[$el['id']] = new RepositoryCustomerDto(
                $el['id'],
                $el['name'],
                $el['phone'],
                $el['email'],
                $el['created_at'],
                $el['updated_at'],
            );
        }

        $stmt->closeCursor();

        return $dtoList;
    }

    public function isCustomerHasTicketsForDay(string $email, string $phone): bool
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(<<<SQL
            SELECT EXISTS(
                select 1
                from tickets t
                inner join customers c ON c.id = t.customer_id
                where (c.email = :email or c.phone = :phone)
                  and t.created_at >= CURRENT_DATE()
                  and t.created_at <  CURRENT_DATE() + interval 1 day
            ) AS has_tickets;
            SQL,
            compact('email', 'phone'),
        );
        $stmt->execute();
        $hasTickets = null;

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hasTickets = $el['has_tickets'];
        }

        $stmt->closeCursor();

        return $hasTickets === 1;
    }
}
