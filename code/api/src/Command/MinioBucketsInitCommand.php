<?php

declare(strict_types=1);

namespace App\Command;

use App\RequestService\S3RequestService;
use Eva\Console\ArgvInput;

readonly class MinioBucketsInitCommand
{
    public function __construct(private S3RequestService $s3RequestService) {}

    public function execute(ArgvInput $argvInput): void
    {
        $this->s3RequestService->createUploadBucket();
        print "Done\n";
    }
}
