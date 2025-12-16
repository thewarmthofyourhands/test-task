<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Repository\TicketRepository\AddTicketDto;
use App\Dto\Repository\TicketRepository\RepositoryTicketDto;
use Eva\Database\ConnectionStoreInterface;
use PDO;

readonly class TicketRepository
{
    public function __construct(private ConnectionStoreInterface $connectionStore)
    {
    }

    public function addTicket(AddTicketDto $addTicketDto): int
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(
            <<<SQL
            insert into tickets (customer_id, topic, text, status, manager_reply_date)
            values (:customer_id, :topic, :text, :status, :manager_reply_date)
            SQL,
            [
                'customer_id' => $addTicketDto->getCustomerId(),
                'topic' => $addTicketDto->getTopic(),
                'text' => $addTicketDto->getText(),
                'status' => $addTicketDto->getStatus(),
                'manager_reply_date' => $addTicketDto->getManagerReplyDate(),
            ],
        );
        $stmt->execute();
        $stmt->closeCursor();

        return (int) $connection->lastInsertId();
    }

    public function changeStatus(int $id, string $status): void
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(
            'update tickets set status = :status where id = :id',
            ['id' => $id,  'status' => $status],
        );
        $stmt->execute();
        $stmt->closeCursor();
    }

    public function findById(int $id): null|RepositoryTicketDto
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(<<<SQL
            select * from tickets where id = :id;
            SQL,
            ['id' => $id],
        );
        $stmt->execute();
        $ticket = null;

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ticket = new RepositoryTicketDto(
                $el['id'],
                $el['customer_id'],
                $el['topic'],
                $el['text'],
                $el['status'],
                $el['manager_reply_date'],
                $el['created_at'],
                $el['updated_at'],
            );
        }

        $stmt->closeCursor();

        return $ticket;
    }

    public function getStatistics(): array
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(<<<SQL
            SELECT
                SUM(
                    CASE
                        WHEN created_at >= NOW() - INTERVAL 1 DAY THEN 1
                        ELSE 0
                    END
                ) AS day_count,
                SUM(
                    CASE
                        WHEN created_at >= NOW() - INTERVAL 7 DAY THEN 1
                        ELSE 0
                    END
                ) AS week_count,
                SUM(
                    CASE
                        WHEN created_at >= NOW() - INTERVAL 1 MONTH THEN 1
                        ELSE 0
                    END
                ) AS month_count
            FROM tickets;
            SQL,
        );
        $stmt->execute();
        $statistics = null;

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statistics = [
                'day' => $el['day_count'],
                'week' => $el['week_count'],
                'month' => $el['month_count'],
            ];
        }

        $stmt->closeCursor();

        return $statistics;
    }

    /**
     * @return RepositoryTicketDto[]
     */
    public function getAll(array $filter): array
    {
        $connection = $this->connectionStore->get();
        $sql = <<<SQL
        select t.*
        from tickets as t
        inner join customers as c on c.id = t.customer_id
        where 1=1
        
        SQL;
        $params = [];

        if (isset($filter['email'])) {
            $sql .= <<<SQL
                and c.email like :email
            SQL;
            $params['email'] = '%' . $filter['email'] . '%';
        }

        if (isset($filter['phone'])) {
            $sql .= <<<SQL
                and c.phone like :phone
            SQL;
            $params['phone'] = '%' . $filter['phone'] . '%';
        }

        if (isset($filter['status'])) {
            $sql .= <<<SQL
                and t.status = :status
            SQL;
            $params['status'] = $filter['status'];
        }

        if (isset($filter['createdAtFrom'])) {
            $sql .= <<<SQL
                and t.created_at >= :createdAtFrom
            SQL;
            $params['createdAtFrom'] = $filter['createdAtFrom'];
        }

        if (isset($filter['createdAtTo'])) {
            $sql .= <<<SQL
                and t.created_at <= :createdAtTo
            SQL;
            $params['createdAtTo'] = $filter['createdAtTo'];
        }

        $stmt = $connection->prepare($sql, $params);
        $stmt->execute();
        $ticketDtoList = [];

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ticketDtoList[] = new RepositoryTicketDto(
                $el['id'],
                $el['customer_id'],
                $el['topic'],
                $el['text'],
                $el['status'],
                $el['manager_reply_date'],
                $el['created_at'],
                $el['updated_at'],
            );
        }

        $stmt->closeCursor();

        return $ticketDtoList;
    }
}
