<?php

declare(strict_types=1);

namespace Migrations;

use Eva\Database\Migrations\AbstractMigration;

class Migration1765660822 extends AbstractMigration
{
    public function up(): void
    {
        $sql = <<<SQL
        create table ticket_files (
            id bigint unsigned not null auto_increment primary key,
            ticket_id bigint unsigned not null,
            name varchar(255) not null,
            path varchar(255) not null unique,
            created_at timestamp default current_timestamp not null,
            updated_at timestamp default current_timestamp not null on update current_timestamp,
            
            constraint fk_files_ticket
                foreign key (ticket_id) references tickets(id)
                on delete cascade
        );
        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL
        drop table ticket_files;
        SQL;

        $this->execute($sql);
    }
}
