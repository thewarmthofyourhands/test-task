<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Eva\Database\ConnectionInterface;
use Eva\Database\ConnectionStoreInterface;
use RuntimeException;

class StatefullConnectionStore implements ConnectionStoreInterface
{
    private array $store = [];

    public function add(string $alias, ConnectionInterface $connection): void
    {
        $this->store[$alias][] = $connection;
    }

    public function get(string $alias = 'default'): ConnectionInterface
    {
        if (false === array_key_exists($alias, $this->store)) {
            throw new RuntimeException('Connection ' . $alias . ' is not exist');
        }

        return $this->store[$alias][random_int(0, count($this->store[$alias]) - 1)];
    }
}
