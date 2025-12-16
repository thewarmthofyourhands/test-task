<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\RequestService\DataForTmpUploadFileDto;
use App\Dto\Service\UploadService\ServiceTicketFileDto;
use App\Repository\TicketFileRepository;
use App\RequestService\S3RequestService;
use DateMalformedStringException;
use DateTimeImmutable;

readonly class UploadService
{
    public function __construct(
        private S3RequestService $s3RequestService,
        private TicketFileRepository $ticketFileRepository,
    ) {}

    public function createTmpUrlForUploadFile(string $fileNameWithExt): DataForTmpUploadFileDto
    {
        return $this->s3RequestService->createTmpUrlForUploadFile($fileNameWithExt);
    }

    public function finalizeUploadedFile(string $key, int $ticketId): void
    {
        $this->s3RequestService->finalizeUploadedFile($key);
        $fileName = $this->s3RequestService->getMetaNameByKey($key);
        $this->ticketFileRepository->addTicketFile($ticketId, $fileName, $key);
    }

    /**
     * @return ServiceTicketFileDto[]
     *
     * @throws DateMalformedStringException
     */
    public function getAttachmentsByTicketId(int $ticketId): array
    {
        $fileDtoList = $this->ticketFileRepository->getTicketFileListByTicketId($ticketId);
        $serviceFileDtoList = [];

        foreach ($fileDtoList as $fileDto) {
            $tmpLink = $this->s3RequestService->getTmpLinkByKey($fileDto->getPath());
            $serviceFileDtoList[] = new ServiceTicketFileDto(
                $fileDto->getId(),
                $fileDto->getTicketId(),
                $fileDto->getPath(),
                $tmpLink,
                new DateTimeImmutable($fileDto->getCreatedAt()),
                new DateTimeImmutable($fileDto->getUpdatedAt()),
            );
        }

        return $serviceFileDtoList;
    }
}
