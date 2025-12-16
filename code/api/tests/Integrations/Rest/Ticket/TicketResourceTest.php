<?php

declare(strict_types=1);

namespace Tests\Integrations\Rest;

use App\Enum\Application\Service\TicketService\TicketStatusEnum;
use App\Enum\Infrastructure\Rest\ApplicationErrorCodeEnum;
use App\Enum\Infrastructure\Rest\ApplicationSuccessCodeEnum;
use App\Enum\Infrastructure\Rest\Messages\ApplicationErrorMessageEnum;
use App\Enum\Infrastructure\Rest\Messages\ApplicationSuccessMessageEnum;
use Eva\Http\HttpMethodsEnum;
use Eva\Http\Message\Request;
use Tests\Integrations\ApiTestCase;

class TicketResourceTest extends ApiTestCase
{
    public function testCreate(): void
    {
        $requestBody = <<<JSON
        {
          "fileNameWithExt": "test.jpg"
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::POST,
            '/api/uploads/tmp',
            [],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::CREATED->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::CREATED->value, $body['code']);

        $fileTmpUrl = $body['data']['url'];
        $fileTmpId = $body['data']['id'];
        $fileTmpTagging = $body['data']['tagging'];
        $testFile = file_get_contents('./var/tests/files/uploads/test.jpg');
        $headerList = [
            'x-amz-tagging' => $fileTmpTagging,
        ];
        $request = new \Eva\Http\Message\Request(
            \Eva\Http\HttpMethodsEnum::PUT,
            $fileTmpUrl,
            $headerList,
            $testFile,
        );
        $client = new \Eva\Http\Client();
        $response = $client->sendRequest($request);
        $this->assertSame($response->getStatusCode(), 200);

        $requestBody = <<<JSON
        {
          "customer": {
            "name": "customer1",
            "phone": "+995551522047",
            "email": "customer1@example.com"
          },
          "topic": "topic1",
          "text": "long text",
          "attachments": ["$fileTmpId"]
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::POST,
            '/api/tickets',
            [],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::CREATED->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::CREATED->value, $body['code']);

        $requestBody = <<<JSON
        {
          "customer": {
            "name": "customer1",
            "phone": "+995551522047",
            "email": "customer1@example.com"
          },
          "topic": "topic2",
          "text": "long text2",
          "attachments": ["$fileTmpId"]
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::POST,
            '/api/tickets',
            [],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(ApplicationErrorMessageEnum::SUBMIT_LIMIT->value, $body['message']);
        $this->assertSame(ApplicationErrorCodeEnum::SUBMIT_LIMIT->value, $body['code']);
        $this->assertSame(null, $body['data']);
    }

    public function testValidation(): void
    {
        $requestBody = <<<JSON
        {
          "fileNameWithExt": "test2.j123pg"
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::POST,
            '/api/uploads/tmp',
            [],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(ApplicationErrorMessageEnum::VALIDATION_ERROR->value, $body['message']);
        $this->assertSame(ApplicationErrorCodeEnum::VALIDATION_ERROR->value, $body['code']);
        $this->assertSame([
            '/fileNameWithExt' => ['The string should match pattern: ^[^\s]+\.(jpg|png|jpeg|webp|gif)$'],
        ], $body['data']);

        $requestBody = <<<JSON
        {
          "customer": {
            "name": "customer1",
            "phone": "+995 551 522 047",
            "email": "customer1@example"
          },
          "topic": "topic1",
          "text": "long text",
          "attachments": ["tickets/693f1418b2abe_test"]
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::POST,
            '/api/tickets',
            [],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(ApplicationErrorMessageEnum::VALIDATION_ERROR->value, $body['message']);
        $this->assertSame(ApplicationErrorCodeEnum::VALIDATION_ERROR->value, $body['code']);
        $this->assertSame([
            '/customer/phone' => ['The string should match pattern: ^\+[1-9]\d{1,14}$'],
        ], $body['data']);
    }

    public function testChangeStatus(): void
    {
        $this->createUser();
        $requestBody = <<<JSON
        {
          "fileNameWithExt": "test.jpg"
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::POST,
            '/api/uploads/tmp',
            [],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);

        $fileTmpUrl = $body['data']['url'];
        $fileTmpId = $body['data']['id'];
        $fileTmpTagging = $body['data']['tagging'];
        $testFile = file_get_contents('./var/tests/files/uploads/test.jpg');
        $headerList = [
            'x-amz-tagging' => $fileTmpTagging,
            'content-type' => 'image/jpeg',
        ];
        $request = new \Eva\Http\Message\Request(
            \Eva\Http\HttpMethodsEnum::PUT,
            $fileTmpUrl,
            $headerList,
            $testFile,
        );
        $client = new \Eva\Http\Client();
        $response = $client->sendRequest($request);
        $this->assertSame($response->getStatusCode(), 200);

        $requestBody = <<<JSON
        {
          "customer": {
            "name": "customer1",
            "phone": "+995551522047",
            "email": "customer1@example.com"
          },
          "topic": "topic1",
          "text": "long text",
          "attachments": ["$fileTmpId"]
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::POST,
            '/api/tickets',
            [],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $id = $body['data']['id'];

        $request = new Request(
            HttpMethodsEnum::GET,
            '/api/tickets/' . $id,
            ['Authorization' => 'token12345'],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::DEFAULT->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::DEFAULT->value, $body['code']);
        $this->assertSame(TicketStatusEnum::NEW->value, $body['data']['status']);

        $status = TicketStatusEnum::IN_PROCESS->value;
        $requestBody = <<<JSON
        {
          "status": "$status"
        }
        JSON;
        $request = new Request(
            HttpMethodsEnum::PATCH,
            '/api/tickets/' . $id,
            ['Authorization' => 'token12345'],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::DEFAULT->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::DEFAULT->value, $body['code']);
        $this->assertSame(null, $body['data']);

        $request = new Request(
            HttpMethodsEnum::GET,
            '/api/tickets/' . $id,
            ['Authorization' => 'token12345'],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::DEFAULT->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::DEFAULT->value, $body['code']);
        $this->assertSame($status, $body['data']['status']);

        $request = new Request(
            HttpMethodsEnum::GET,
            '/api/tickets',
            ['Authorization' => 'token12345'],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::DEFAULT->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::DEFAULT->value, $body['code']);

        $request = new Request(
            HttpMethodsEnum::GET,
            '/api/tickets?filter[email]=customer&filter[phone]=551&filter[status]=IN_PROCESS&filter[createdAtTo]=2026-01-01 23:23:23&filter[createdAtFrom]=2022-01-01 22:22:22',
            ['Authorization' => 'token12345'],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::DEFAULT->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::DEFAULT->value, $body['code']);

        $request = new Request(
            HttpMethodsEnum::GET,
            '/api/tickets/statistics',
            ['Authorization' => 'token12345'],
            $requestBody,
        );
        $response = $this->application->handle($request);
        $body = json_decode($response->getBody(), true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(ApplicationSuccessMessageEnum::DEFAULT->value, $body['message']);
        $this->assertSame(ApplicationSuccessCodeEnum::DEFAULT->value, $body['code']);
    }
}
