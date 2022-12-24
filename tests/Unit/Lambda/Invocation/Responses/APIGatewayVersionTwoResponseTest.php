<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\APIGatewayVersionTwoResponse;

final class APIGatewayVersionTwoResponseTest extends TestCase
{
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
