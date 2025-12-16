<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Repository\TicketFileRepository\RepositoryTicketFileDto;
use Eva\Database\ConnectionStoreInterface;
use PDO;

readonly class TicketFileRepository
{
    public function __construct(private ConnectionStoreInterface $connectionStore)
    {
    }

    public function addTicketFile(int $ticketId, string $fileName, string $key): int
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(
            <<<SQL
            insert into ticket_files (ticket_id, name, path)
            values (:ticket_id, :name, :path)
            SQL,
            [
                'ticket_id' => $ticketId,
                'name' => $fileName,
                'path' => $key,
            ],
        );
        $stmt->execute();
        $stmt->closeCursor();

        return (int) $connection->lastInsertId();
    }

    /**
     * @return RepositoryTicketFileDto[]
     */
    public function getTicketFileListByTicketId(int $ticketId): array
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(<<<SQL
            select * from ticket_files where ticket_id = :ticket_id;
            SQL,
            ['ticket_id' => $ticketId],
        );
        $stmt->execute();
        $dtoList = [];

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dtoList[] = new RepositoryTicketFileDto(
                $el['id'],
                $el['ticket_id'],
                $el['name'],
                $el['path'],
                $el['created_at'],
                $el['updated_at'],
            );
        }

        $stmt->closeCursor();

        return $dtoList;
    }
}
