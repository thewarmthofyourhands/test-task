<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Repository\UserRepository\UserDto;
use Eva\Database\ConnectionStoreInterface;
use PDO;

readonly class UserRepository
{
    public function __construct(private ConnectionStoreInterface $connectionStore)
    {
    }

    public function findUserByToken(string $token): null|UserDto
    {
        $connection = $this->connectionStore->get();
        $stmt = $connection->prepare(<<<SQL
            select * from users where token = :token;
            SQL,
        );
        $stmt->execute(['token' => $token]);
        $user = null;

        while ($el = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new UserDto(
                $el['id'],
                $el['name'],
                $el['email'],
                $el['password'],
                $el['token'],
                $el['created_at'],
                $el['updated_at'],
            );
        }

        $stmt->closeCursor();

        return $user;
    }
}
