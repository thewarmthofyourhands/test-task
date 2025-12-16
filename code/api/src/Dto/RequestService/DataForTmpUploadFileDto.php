<?php

declare(strict_types=1);

namespace App\Dto\RequestService;

final readonly class DataForTmpUploadFileDto
{
    public function __construct(
        private string $url,
        private string $id,
        private string $tagging,
    ) {}

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTagging(): string
    {
        return $this->tagging;
    }
}
