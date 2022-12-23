<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionTwoEvent;

final class APIGatewayVersionTwoEventTest extends TestCase
{
    /** @test */
    public function it_forms_event_without_multi_value_support(): void
    {
        $data = [
            'rawPath' => '/foo',
            'rawQueryString' => 'foo=bar',
            'cookies' => [
                'foo',
            ],
            'headers' => [
                'foo' => 'bar',
            ],
            'requestContext' => [
                'http' => [
                    'method' => 'GET',
                ],
            ],
            'isBase64Encoded' => false,
            'body' => 'foo',
        ];

        $event = APIGatewayVersionTwoEvent::fromResponseData($data);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertSame('GET', $event->getMethod());
        self::assertSame('/foo', $event->getPath());
        self::assertSame('foo=bar', $event->getQueryString());
        self::assertSame([
            'foo' => ['bar'],
            'cookie' => ['foo'],
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
            'rawPath' => '/foo',
            'rawQueryString' => 'foo=bar',
            'cookies' => [
                'foo',
            ],
            'headers' => [
                'foo' => 'bar',
            ],
            'requestContext' => [
                'http' => [
                    'method' => 'GET',
                ],
            ],
            'isBase64Encoded' => true,
            'body' => base64_encode('foo'),
        ];

        $event = APIGatewayVersionTwoEvent::fromResponseData($data);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertSame('GET', $event->getMethod());
        self::assertSame('/foo', $event->getPath());
        self::assertSame('foo=bar', $event->getQueryString());
        self::assertSame([
            'foo' => ['bar'],
            'cookie' => ['foo'],
            'content-type' => ['application/x-www-form-urlencoded'],
            'content-length' => ['3'],
        ], $event->getHeaders());
        self::assertTrue($event->isBase64Encoded());
        self::assertSame('foo', $event->getBody());
    }
}
