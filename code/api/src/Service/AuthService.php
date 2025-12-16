<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Repository\UserRepository\UserDto;
use App\Exception\Application\AuthenticationException;
use App\Repository\UserRepository;

readonly class AuthService
{
    public function __construct(private UserRepository $userRepository) {}

    public function getAuthUser(string $token): UserDto
    {
        $userDto = $this->userRepository->findUserByToken($token);

        if (null === $userDto) {
            throw new AuthenticationException();
        }

        return $userDto;
    }
}
