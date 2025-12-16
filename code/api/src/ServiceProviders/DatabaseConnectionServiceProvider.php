<?php

declare(strict_types=1);

namespace App\ServiceProviders;

use App\Infrastructure\Database\StatefullConnection;
use Eva\Database\ConnectionStoreInterface;
use Eva\DependencyInjection\ContainerInterface;

class DatabaseConnectionServiceProvider
{
    public function __construct(ConnectionStoreInterface $connectionStore, ContainerInterface $container)
    {
        $env = $container->getParameter('env');

        for ($i = 1; $i <= 1; $i++) {
            $connectionStore->add(
                'default',
                new StatefullConnection(
                    $env['DB_HOST'],
                    $env['DB_PORT'],
                    $env['DB_NAME'],
                    $env['DB_USERNAME'],
                    $env['DB_PASSWORD'],
                ),
            );
        }
    }
}
