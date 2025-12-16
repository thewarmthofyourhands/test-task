<?php

declare(strict_types=1);

namespace Migrations;

use Eva\Database\Migrations\AbstractMigration;

class Migration1765655409 extends AbstractMigration
{
    public function up(): void
    {
        $sql = <<<SQL
        create table users (
            id bigint unsigned not null auto_increment primary key,
            name varchar(255) not null,
            email varchar(255) not null unique,
            password varchar(255) not null,
            token varchar(255) null,
            created_at timestamp default current_timestamp not null,
            updated_at timestamp default current_timestamp not null on update current_timestamp
        );
        create table customers (
            id bigint unsigned not null auto_increment primary key,
            name varchar(255) not null,
            phone varchar(20) not null,
            email varchar(255) not null,
            created_at timestamp default current_timestamp not null,
            updated_at timestamp default current_timestamp not null on update current_timestamp
        );
        create table tickets (
            id bigint unsigned not null auto_increment primary key,
            customer_id bigint unsigned not null,
            topic varchar(255) not null,
            text text not null,
            status varchar(50) not null,
            manager_reply_date datetime null,
            created_at timestamp default current_timestamp not null,
            updated_at timestamp default current_timestamp not null on update current_timestamp,
            
            constraint fk_tickets_customer
                foreign key (customer_id) references customers(id)
                on delete cascade
        );
        SQL;

        $this->execute($sql);
    }

    public function down(): void
    {
        $sql = <<<SQL
        drop table tickets;
        drop table customers;
        drop table users;
        SQL;

        $this->execute($sql);
    }
}
