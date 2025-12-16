<?php

declare(strict_types=1);

namespace App\RequestService;

use App\Dto\RequestService\DataForTmpUploadFileDto;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

readonly class S3RequestService
{
    private const CUSTOMERS_DIR = 'tickets';
    public const DEFAULT_BUCKET = 'uploads';

    private S3Client $s3Client;
    private S3Client $publicS3Client;

    public function __construct(string $endpoint, string $internalEndpoint, string $key, string $secret)
    {
        $this->publicS3Client = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => $internalEndpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);
    }

    public function createTmpUrlForUploadFile(string $fileNameWithExt): DataForTmpUploadFileDto
    {
        $key = sprintf('%s/%s_%s', self::CUSTOMERS_DIR, uniqid(), $fileNameWithExt);
        $tagging = 'state=tmp';
        $cmd = $this->publicS3Client->getCommand('PutObject', [
            'Bucket' => self::DEFAULT_BUCKET,
            'Key' => $key,
            'Tagging' => $tagging,
            'Metadata' => [
                'original-name' => $fileNameWithExt,
            ],
        ]);
        $request = $this->publicS3Client->createPresignedRequest($cmd, '+10 minutes');
        $url = (string) $request->getUri();

        return new DataForTmpUploadFileDto($url, $key, $tagging);
    }

    public function finalizeUploadedFile(string $key): void
    {
        $this->s3Client->putObjectTagging([
            'Bucket' => self::DEFAULT_BUCKET,
            'Key'    => $key,
            'Tagging' => [
                'TagSet' => [
                    ['Key' => 'state', 'Value' => 'final'],
                ],
            ],
        ]);
    }

    public function createUploadBucket(): void
    {
        try {
            $bucket = self::DEFAULT_BUCKET;
            $this->s3Client->headBucket(['Bucket' => $bucket]);
        } catch (AwsException $e) {
            if ($e->getStatusCode() === 404) {
                $this->s3Client->createBucket(['Bucket' => $bucket]);
                $this->s3Client->waitUntil('BucketExists', ['Bucket' => $bucket]);
            } else {
                throw $e;
            }
        }
    }

    public function getMetaNameByKey(string $key): string
    {
        $res = $this->s3Client->headObject([
            'Bucket' => self::DEFAULT_BUCKET,
            'Key' => $key,
        ]);

        return $res['Metadata']['original-name'] ?? 'Unknown';
    }

    public function getTmpLinkByKey(string $key): string
    {
        $cmd = $this->publicS3Client->getCommand('GetObject', [
            'Bucket' => self::DEFAULT_BUCKET,
            'ResponseContentDisposition' => 'inline',
//            'ResponseContentType' => 'image/jpeg',
            'Key' => $key,
        ]);

        $request = $this->publicS3Client->createPresignedRequest(
            $cmd,
            '+15 minutes'
        );

        return (string) $request->getUri();
    }
}
