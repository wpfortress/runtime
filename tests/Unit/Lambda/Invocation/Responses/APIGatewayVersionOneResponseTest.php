<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpErrorResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationStaticFileResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\APIGatewayVersionOneResponse;

final class APIGatewayVersionOneResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_response_from_fastcgi_response(): void
    {
        $fastCGIResponse = $this->createMock(ProvidesResponseData::class);
        $fastCGIResponse
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([
                'Content-Type' => ['text/html; charset=utf-8'],
                'Status' => ['200 OK'],
            ]);
        $fastCGIResponse
            ->expects(self::once())
            ->method('getBody')
            ->willReturn('foo');

        $response = APIGatewayVersionOneResponse::fromFastCGIResponse($fastCGIResponse);
        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals(['Content-Type' => ['text/html; charset=utf-8']], $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_from_http_error_response(): void
    {
        $errorResponse = $this->createMock(LambdaInvocationHttpErrorResponseContract::class);
        $errorResponse
            ->expects(self::once())
            ->method('getBody')
            ->willReturn('foo');
        $errorResponse
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([
                'Content-Type' => ['text/html; charset=utf-8'],
            ]);
        $errorResponse
            ->expects(self::once())
            ->method('getStatus')
            ->willReturn(HttpStatus::NOT_FOUND);

        $response = APIGatewayVersionOneResponse::fromHttpErrorResponse($errorResponse);
        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::NOT_FOUND, $result['statusCode']);
        self::assertEquals(['Content-Type' => ['text/html; charset=utf-8']], $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_from_static_file_response(): void
    {
        $staticFileResponse = $this->createMock(LambdaInvocationStaticFileResponseContract::class);
        $staticFileResponse
            ->expects(self::once())
            ->method('getBody')
            ->willReturn('foo');
        $staticFileResponse
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([
                'Content-Type' => ['text/plain'],
            ]);

        $response = APIGatewayVersionOneResponse::fromStaticResponse($staticFileResponse);
        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertTrue($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals(['Content-Type' => ['text/plain']], $result['multiValueHeaders']);
        self::assertSame('Zm9v', $result['body']);
    }

    /** @test */
    public function it_forms_correct_default_response(): void
    {
        $response = new APIGatewayVersionOneResponse(
            body: 'foo',
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals(new stdClass(), $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_encoded_body(): void
    {
        $response = new APIGatewayVersionOneResponse(
            body: 'foo',
            isBase64Encoded: true,
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertTrue($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals(new stdClass(), $result['multiValueHeaders']);
        self::assertSame('Zm9v', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_custom_headers(): void
    {
        $response = new APIGatewayVersionOneResponse(
            body: 'foo',
            multiValueHeaders: ['content-type' => ['application/json']],
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertSame(['content-type' => ['application/json']], $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_custom_status(): void
    {
        $response = new APIGatewayVersionOneResponse(
            body: 'foo',
            status: HttpStatus::ACCEPTED,
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::ACCEPTED, $result['statusCode']);
        self::assertEquals(new stdClass(), $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }
}
