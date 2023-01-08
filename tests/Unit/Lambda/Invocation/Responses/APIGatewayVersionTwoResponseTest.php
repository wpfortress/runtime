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
use WPFortress\Runtime\Lambda\Invocation\Responses\APIGatewayVersionTwoResponse;

final class APIGatewayVersionTwoResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_response_from_fastcgi_response(): void
    {
        $fastCGIResponse = $this->createMock(ProvidesResponseData::class);
        $fastCGIResponse
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([
                'Set-Cookie' => ['foo=bar'],
                'Set-cookie' => ['bar=foo'],
                'Content-Type' => ['text/html; charset=utf-8'],
                'Status' => ['200 OK'],
            ]);
        $fastCGIResponse
            ->expects(self::once())
            ->method('getBody')
            ->willReturn('foo');

        $response = APIGatewayVersionTwoResponse::fromFastCGIResponse($fastCGIResponse);
        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals(['foo=bar', 'bar=foo'], $result['cookies']);
        self::assertEquals(['Content-Type' => 'text/html; charset=utf-8'], $result['headers']);
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

        $response = APIGatewayVersionTwoResponse::fromHttpErrorResponse($errorResponse);
        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::NOT_FOUND, $result['statusCode']);
        self::assertEquals([], $result['cookies']);
        self::assertEquals(['Content-Type' => 'text/html; charset=utf-8'], $result['headers']);
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

        $response = APIGatewayVersionTwoResponse::fromStaticResponse($staticFileResponse);
        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertTrue($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals([], $result['cookies']);
        self::assertEquals(['Content-Type' => 'text/plain'], $result['headers']);
        self::assertSame('Zm9v', $result['body']);
    }

    /** @test */
    public function it_forms_correct_default_response(): void
    {
        $response = new APIGatewayVersionTwoResponse(
            body: 'foo',
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals([], $result['cookies']);
        self::assertEquals(new stdClass(), $result['headers']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_encoded_body(): void
    {
        $response = new APIGatewayVersionTwoResponse(
            body: 'foo',
            isBase64Encoded: true,
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertTrue($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals([], $result['cookies']);
        self::assertEquals(new stdClass(), $result['headers']);
        self::assertSame('Zm9v', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_custom_cookies(): void
    {
        $response = new APIGatewayVersionTwoResponse(
            body: 'foo',
            cookies: ['foo=bar'],
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertSame(['foo=bar'], $result['cookies']);
        self::assertEquals(new stdClass(), $result['headers']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_custom_headers(): void
    {
        $response = new APIGatewayVersionTwoResponse(
            body: 'foo',
            headers: ['content-type' => 'application/json'],
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertEquals([], $result['cookies']);
        self::assertEquals(['content-type' => 'application/json'], $result['headers']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_custom_status(): void
    {
        $response = new APIGatewayVersionTwoResponse(
            body: 'foo',
            status: HttpStatus::ACCEPTED,
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::ACCEPTED, $result['statusCode']);
        self::assertEquals([], $result['cookies']);
        self::assertEquals(new stdClass(), $result['headers']);
        self::assertSame('foo', $result['body']);
    }
}
