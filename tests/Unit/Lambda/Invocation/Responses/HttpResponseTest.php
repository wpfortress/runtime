<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Lambda\Invocation\Responses\HttpResponse;

final class HttpResponseTest extends TestCase
{
    /** @test */
    public function it_coverts_response_to_api_gateway_format(): void
    {
        $response1 = new HttpResponse(
            body: '<p>Hello world!</p>',
            headers: ['Content-Type' => 'text/html; charset=utf-8'],
            formatVersion: 1.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'multiValueHeaders' => [
                'Content-Type' => ['text/html; charset=utf-8'],
            ],
            'body' => '<p>Hello world!</p>',
        ], $response1->toApiGatewayFormat());

        $response2 = new HttpResponse(
            body: '<p>Hello world!</p>',
            headers: ['Content-Type' => 'text/html; charset=utf-8'],
            formatVersion: 2.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'headers' => [
                'Content-Type' => 'text/html; charset=utf-8',
            ],
            'body' => '<p>Hello world!</p>',
        ], $response2->toApiGatewayFormat());
    }

    /** @test */
    public function it_capitalizes_header_names(): void
    {
        $response1 = new HttpResponse(
            body: '',
            headers: ['x-foo' => 'bar'],
            formatVersion: 1.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'multiValueHeaders' => [
                'X-Foo' => ['bar'],
                'Content-Type' => ['text/html'],
            ],
            'body' => '',
        ], $response1->toApiGatewayFormat());

        $response2 = new HttpResponse(
            body: '',
            headers: ['x-foo' => 'bar'],
            formatVersion: 2.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'headers' => [
                'X-Foo' => 'bar',
                'Content-Type' => 'text/html',
            ],
            'body' => '',
        ], $response2->toApiGatewayFormat());
    }

    /** @test */
    public function it_flattens_nested_arrays_in_headers(): void
    {
        $response1 = new HttpResponse(
            body: '',
            headers: ['foo' => ['bar', 'baz']],
            formatVersion: 1.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'multiValueHeaders' => [
                'Foo' => ['bar', 'baz'],
                'Content-Type' => ['text/html'],
            ],
            'body' => '',
        ], $response1->toApiGatewayFormat());

        $response2 = new HttpResponse(
            body: '',
            headers: ['foo' => ['bar', 'baz']],
            formatVersion: 2.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'headers' => [
                'Foo' => 'baz',
                'Content-Type' => 'text/html',
            ],
            'body' => '',
        ], $response2->toApiGatewayFormat());
    }

    /** @test */
    public function it_converts_response_with_single_cookie_to_api_gateway_format(): void
    {
        $response1 = new HttpResponse(
            body: '',
            headers: ['set-cookie' => 'foo'],
            formatVersion: 1.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'multiValueHeaders' => [
                'Set-Cookie' => ['foo'],
                'Content-Type' => ['text/html'],
            ],
            'body' => '',
        ], $response1->toApiGatewayFormat());

        $response2 = new HttpResponse(
            body: '',
            headers: ['set-cookie' => 'foo'],
            formatVersion: 2.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'headers' => [
                'Content-Type' => 'text/html',
            ],
            'cookies' => ['foo'],
            'body' => '',
        ], $response2->toApiGatewayFormat());
    }

    /** @test */
    public function it_converts_response_with_multiple_cookies_to_api_gateway_format(): void
    {
        $response1 = new HttpResponse(
            body: '',
            headers: ['set-cookie' => ['foo', 'bar']],
            formatVersion: 1.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'multiValueHeaders' => [
                'Set-Cookie' => ['foo', 'bar'],
                'Content-Type' => ['text/html'],
            ],
            'body' => '',
        ], $response1->toApiGatewayFormat());

        $response2 = new HttpResponse(
            body: '',
            headers: ['set-cookie' => ['foo', 'bar']],
            formatVersion: 2.0,
        );

        self::assertEquals([
            'isBase64Encoded' => false,
            'statusCode' => 200,
            'headers' => [
                'Content-Type' => 'text/html',
            ],
            'cookies' => ['foo', 'bar'],
            'body' => '',
        ], $response2->toApiGatewayFormat());
    }
}
