<?php

declare(strict_types=1);

namespace App\Enum\Application\Service\TicketService;

use ValueError;

enum TicketStatusEnum: string
{
    case NEW = 'NEW';
    case IN_PROCESS = 'IN_PROCESS';
    case DONE = 'DONE';

    public static function tryFromName(string $name): null|self
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name){
                return $status;
            }
        }

        return null;
    }

    public static function fromName(string $name): self
    {
        $value = self::tryFromName($name);

        if (null === $value) {
            throw new ValueError("$name is not a valid backing value for enum " . self::class );
        }

        return $value;
    }
}
