<?php

declare(strict_types=1);

namespace App\UseCase\Upload;

use App\Dto\RequestService\DataForTmpUploadFileDto;
use App\Service\UploadService;

readonly class CreateTmpUrlForUploadFileHandler
{
    public function __construct(
        private UploadService $uploadService,
    ) {}


    public function handle(string $fileNameWithExt): DataForTmpUploadFileDto
    {
        return $this->uploadService->createTmpUrlForUploadFile($fileNameWithExt);
    }
}
