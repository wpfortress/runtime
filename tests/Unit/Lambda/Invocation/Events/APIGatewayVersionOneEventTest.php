<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionOneEvent;

final class APIGatewayVersionOneEventTest extends TestCase
{
    /** @test */
    public function it_forms_event_without_multi_value_support(): void
    {
        $data = [
            'httpMethod' => 'GET',
            'path' => '/foo',
            'queryStringParameters' => [
                'foo' => 'bar',
            ],
            'headers' => [
                'foo' => 'bar',
            ],
            'isBase64Encoded' => false,
            'body' => 'foo',
        ];

        $event = APIGatewayVersionOneEvent::fromResponseData($data);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertInstanceOf(InvocationHttpEventContract::class, $event);
        self::assertSame('GET', $event->getMethod());
        self::assertSame('/foo', $event->getPath());
        self::assertSame('foo=bar', $event->getQueryString());
        self::assertFalse($event->usesMultiValueHeaders());
        self::assertSame([
            'foo' => ['bar'],
            'content-type' => ['application/x-www-form-urlencoded'],
            'content-length' => ['3'],
        ], $event->getHeaders());
        self::assertFalse($event->isBase64Encoded());
        self::assertSame('foo', $event->getBody());
    }

    /** @test */
    public function it_forms_event_with_multi_value_support(): void
    {
        $data = [
            'httpMethod' => 'GET',
            'path' => '/foo',
            'multiValueQueryStringParameters' => [
                'foo' => ['bar', 'baz'],
            ],
            'multiValueHeaders' => [
                'foo' => ['bar', 'baz'],
            ],
            'isBase64Encoded' => false,
            'body' => 'foo',
        ];

        $event = APIGatewayVersionOneEvent::fromResponseData($data);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertInstanceOf(InvocationHttpEventContract::class, $event);
        self::assertSame('GET', $event->getMethod());
        self::assertSame('/foo', $event->getPath());
        self::assertSame('foo=baz', $event->getQueryString());
        self::assertTrue($event->usesMultiValueHeaders());
        self::assertSame([
            'foo' => ['bar', 'baz'],
            'content-type' => ['application/x-www-form-urlencoded'],
            'content-length' => ['3'],
        ], $event->getHeaders());
        self::assertFalse($event->isBase64Encoded());
        self::assertSame('foo', $event->getBody());
    }

    /** @test */
    public function it_forms_event_with_encrypted_body(): void
    {
        $data = [
            'httpMethod' => 'GET',
            'path' => '/foo',
            'queryStringParameters' => [
                'foo' => 'bar',
            ],
            'headers' => [
                'foo' => 'bar',
            ],
            'isBase64Encoded' => true,
            'body' => base64_encode('foo'),
        ];

        $event = APIGatewayVersionOneEvent::fromResponseData($data);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertInstanceOf(InvocationHttpEventContract::class, $event);
        self::assertSame('GET', $event->getMethod());
        self::assertSame('/foo', $event->getPath());
        self::assertSame('foo=bar', $event->getQueryString());
        self::assertFalse($event->usesMultiValueHeaders());
        self::assertSame([
            'foo' => ['bar'],
            'content-type' => ['application/x-www-form-urlencoded'],
            'content-length' => ['3'],
        ], $event->getHeaders());
        self::assertTrue($event->isBase64Encoded());
        self::assertSame('foo', $event->getBody());
    }
}
