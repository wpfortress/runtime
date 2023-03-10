<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionTwoEvent;

final class APIGatewayVersionTwoEventTest extends TestCase
{
    /** @test */
    public function it_should_handle_given_response_data(): void
    {
        $shouldHandle = APIGatewayVersionTwoEvent::shouldHandle([
            'version' => '2.0',
            'requestContext' => [],
        ]);

        self::assertTrue($shouldHandle);

        $shouldHandle = APIGatewayVersionTwoEvent::shouldHandle([
            'version' => '1.0',
            'requestContext' => [],
        ]);

        self::assertFalse($shouldHandle);
    }

    /** @test */
    public function it_forms_correct_event(): void
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

        self::assertInstanceOf(LambdaInvocationEventContract::class, $event);
        self::assertInstanceOf(LambdaInvocationHttpEventContract::class, $event);
        self::assertSame('GET', $event->getRequestMethod());
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

        self::assertInstanceOf(LambdaInvocationEventContract::class, $event);
        self::assertInstanceOf(LambdaInvocationHttpEventContract::class, $event);
        self::assertSame('GET', $event->getRequestMethod());
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
