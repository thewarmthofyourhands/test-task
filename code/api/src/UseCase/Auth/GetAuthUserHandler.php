<?php

declare(strict_types=1);

namespace App\UseCase\Auth;

use App\Dto\Repository\UserRepository\UserDto;
use App\Service\AuthService;

readonly class GetAuthUserHandler
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function handle(string $token): UserDto
    {
        return $this->authService->getAuthUser($token);
    }
}