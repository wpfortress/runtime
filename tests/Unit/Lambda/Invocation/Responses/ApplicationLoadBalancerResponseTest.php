<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Contracts\InvocationStaticFileResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpErrorResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\ApplicationLoadBalancerResponse;

final class ApplicationLoadBalancerResponseTest extends TestCase
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

        $response = ApplicationLoadBalancerResponse::fromFastCGIResponse($fastCGIResponse);
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

        $response = ApplicationLoadBalancerResponse::fromHttpErrorResponse($errorResponse);
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
        $staticFileResponse = $this->createMock(InvocationStaticFileResponseContract::class);
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

        $response = ApplicationLoadBalancerResponse::fromStaticResponse($staticFileResponse);
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
        $response = new ApplicationLoadBalancerResponse(
            body: 'foo',
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertSame('200 OK', $result['statusDescription']);
        self::assertEquals(new stdClass(), $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_encoded_body(): void
    {
        $response = new ApplicationLoadBalancerResponse(
            body: 'foo',
            isBase64Encoded: true,
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertTrue($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertSame('200 OK', $result['statusDescription']);
        self::assertEquals(new stdClass(), $result['multiValueHeaders']);
        self::assertSame('Zm9v', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_custom_headers(): void
    {
        $response = new ApplicationLoadBalancerResponse(
            body: 'foo',
            multiValueHeaders: ['content-type' => ['application/json']],
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::OK, $result['statusCode']);
        self::assertSame('200 OK', $result['statusDescription']);
        self::assertSame(['content-type' => ['application/json']], $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }

    /** @test */
    public function it_forms_correct_response_with_custom_status(): void
    {
        $response = new ApplicationLoadBalancerResponse(
            body: 'foo',
            status: HttpStatus::ACCEPTED,
        );

        $result = $response->jsonSerialize();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertFalse($result['isBase64Encoded']);
        self::assertSame(HttpStatus::ACCEPTED, $result['statusCode']);
        self::assertSame('202 Accepted', $result['statusDescription']);
        self::assertEquals(new stdClass(), $result['multiValueHeaders']);
        self::assertSame('foo', $result['body']);
    }
}
