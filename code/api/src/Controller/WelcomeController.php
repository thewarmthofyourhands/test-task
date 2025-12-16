<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Infrastructure\Rest\ApplicationSuccessCodeEnum;
use App\Enum\Infrastructure\Rest\Messages\ApplicationSuccessMessageEnum;
use App\Infrastructure\Rest\ApiResponse;
use Eva\Http\Message\Request;
use Eva\Http\Message\Response;

readonly class WelcomeController
{
    public function index(Request $request): Response
    {
        return (new ApiResponse(
            ['message' => 'Hi'],
            ApplicationSuccessCodeEnum::DEFAULT->value,
            ApplicationSuccessMessageEnum::DEFAULT->value,
        ))->build();
    }
}
