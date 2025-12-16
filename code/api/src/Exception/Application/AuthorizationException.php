<?php

declare(strict_types=1);

namespace App\Exception\Application;

use App\Enum\Infrastructure\Rest\ApplicationErrorCodeEnum;

class AuthorizationException extends ApplicationException
{
    public function __construct(null|ApplicationErrorCodeEnum $code = null)
    {
        parent::__construct($code ?? ApplicationErrorCodeEnum::AUTHORIZATION_ERROR);
    }
}
