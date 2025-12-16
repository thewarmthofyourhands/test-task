<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Infrastructure\Rest\ApplicationSuccessCodeEnum;
use App\Enum\Infrastructure\Rest\Messages\ApplicationSuccessMessageEnum;
use App\Exception\Validation\ValidatorException;
use App\Infrastructure\Rest\ApiResponse;
use App\UseCase\Upload\CreateTmpUrlForUploadFileHandler;
use App\Validation\Validator;
use Eva\Http\Message\Request;
use Eva\Http\Message\Response;
use Eva\Http\Parser\JsonRequestParser;
use JsonException;

readonly class UploadController
{
    public function __construct(
        private CreateTmpUrlForUploadFileHandler $createTmpUrlForUploadFileHandler,
        private Validator $validator,
    ) {}

    /**
     * @throws JsonException
     * @throws ValidatorException
     */
    public function storeTmp(Request $request): Response
    {
        $body = JsonRequestParser::parseBody($request);
        $this->validator->validate($body, '/Rest/UploadController/StoreTmp.json');
        $fileNameWithExt = $body['fileNameWithExt'];
        $dataForTmpUploadFileDto = $this->createTmpUrlForUploadFileHandler->handle($fileNameWithExt);

        return (new ApiResponse(
            [
                'url' => $dataForTmpUploadFileDto->getUrl(),
                'id' => $dataForTmpUploadFileDto->getId(),
                'tagging' => $dataForTmpUploadFileDto->getTagging(),
            ],
            ApplicationSuccessCodeEnum::CREATED->value,
            ApplicationSuccessMessageEnum::CREATED->value,
        ))->build(201);
    }
}
