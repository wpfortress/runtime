<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\HttpEvent;

final class HttpEventTest extends TestCase
{
    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertSame($expectedData, $event->getData());
    }

    /** @test */
    public function it_resolves_default_protocol(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame('HTTP/1.1', $event->getProtocol());
    }

    /** @test */
    public function it_resolves_protocol_from_payload_version_1(): void
    {
        $expectedData = ['requestContext' => ['protocol' => 'foo']];

        $event = new HttpEvent($expectedData);

        self::assertSame('foo', $event->getProtocol());
    }

    /** @test */
    public function it_resolves_protocol_from_payload_version_2(): void
    {
        $expectedData = ['requestContext' => ['http' => ['protocol' => 'foo']]];

        $event = new HttpEvent($expectedData);

        self::assertSame('foo', $event->getProtocol());
    }

    /** @test */
    public function it_resolves_default_method(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame('GET', $event->getMethod());
    }

    /** @test */
    public function it_resolves_method_from_payload_version_1(): void
    {
        $expectedData = ['httpMethod' => 'POST'];

        $event = new HttpEvent($expectedData);

        self::assertSame('POST', $event->getMethod());
    }

    /** @test */
    public function it_resolves_method_from_payload_version_2(): void
    {
        $expectedData = ['requestContext' => ['http' => ['method' => 'POST']]];

        $event = new HttpEvent($expectedData);

        self::assertSame('POST', $event->getMethod());
    }

    /** @test */
    public function it_resolves_default_path(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame('/', $event->getPath());
    }

    /** @test */
    public function it_resolves_path_from_payload_version_1(): void
    {
        $expectedData = ['path' => '/foo'];

        $event = new HttpEvent($expectedData);

        self::assertSame('/foo', $event->getPath());
    }

    /** @test */
    public function it_resolves_path_from_payload_version_2(): void
    {
        $expectedData = ['rawPath' => '/foo'];

        $event = new HttpEvent($expectedData);

        self::assertSame('/foo', $event->getPath());
    }

    /** @test */
    public function it_resolves_uri_without_query_string(): void
    {
        $expectedData = ['path' => '/foo'];

        $event = new HttpEvent($expectedData);

        self::assertSame('/foo', $event->getUri());
    }

    /** @test */
    public function it_resolves_uri_with_query_string(): void
    {
        $expectedData = ['path' => '/foo', 'rawQueryString' => 'foo[]=bar&foo[]=baz'];

        $event = new HttpEvent($expectedData);

        self::assertSame('/foo?foo%5B0%5D=bar&foo%5B1%5D=baz', $event->getUri());
    }

    /** @test */
    public function it_resolves_default_query_string(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame('', $event->getQueryString());
    }

    /** @test */
    public function it_resolves_query_string_from_payload_version_2(): void
    {
        $expectedData = ['rawQueryString' => 'foo[]=bar&foo[]=baz'];

        $event = new HttpEvent($expectedData);

        self::assertSame('foo%5B0%5D=bar&foo%5B1%5D=baz', $event->getQueryString());
    }

    /** @test */
    public function it_resolves_query_string_from_multi_value_query_string_parameters(): void
    {
        $expectedData = ['multiValueQueryStringParameters' => ['foo[]' => ['bar', 'baz%']]];

        $event = new HttpEvent($expectedData);

        self::assertSame('foo%5B0%5D=bar&foo%5B1%5D=baz%25', $event->getQueryString());
    }

    /** @test */
    public function it_resolves_query_string_from_query_string_parameters(): void
    {
        $expectedData = ['queryStringParameters' => ['foo[]' => ['bar', 'baz%']]];

        $event = new HttpEvent($expectedData);

        self::assertSame('foo%5B0%5D=bar&foo%5B1%5D=baz%25', $event->getQueryString());
    }

    /** @test */
    public function it_resolves_default_headers(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame([], $event->getHeaders());
    }

    /** @test */
    public function it_resolves_headers_from_multi_value_headers(): void
    {
        $expectedData = ['multiValueHeaders' => $expectedHeaders = ['foo' => ['bar']]];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedHeaders, $event->getHeaders());
    }

    /** @test */
    public function it_resolves_headers_from_headers(): void
    {
        $expectedData = ['headers' => ['foo' => 'bar']];

        $event = new HttpEvent($expectedData);

        self::assertSame(['foo' => ['bar']], $event->getHeaders());
    }

    /** @test */
    public function it_resolves_headers_with_content_type(): void
    {
        $expectedData = ['multiValueHeaders' => ['foo' => ['bar']], 'body' => 'foo'];

        $event = new HttpEvent($expectedData);

        self::assertSame(
            ['foo' => ['bar'], 'content-type' => ['application/x-www-form-urlencoded'], 'content-length' => [3]],
            $event->getHeaders()
        );
    }

    /** @test */
    public function it_resolves_default_content_type(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertNull($event->getContentType());
    }

    /** @test */
    public function it_resolves_content_type(): void
    {
        $expectedData = ['multiValueHeaders' => ['content-type' => [$expectedContentType = 'text/plain']]];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedContentType, $event->getContentType());
    }

    /** @test */
    public function it_resolves_default_server_port(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame(80, $event->getServerPort());
    }

    /** @test */
    public function it_resolves_server_port(): void
    {
        $expectedData = ['multiValueHeaders' => ['x-forwarded-port' => [$expectedServerPort = 443]]];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedServerPort, $event->getServerPort());
    }

    /** @test */
    public function it_resolves_default_server_name(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame('localhost', $event->getServerName());
    }

    /** @test */
    public function it_resolves_server_name(): void
    {
        $expectedData = ['multiValueHeaders' => ['host' => [$expectedServerName = 'foo']]];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedServerName, $event->getServerName());
    }

    /** @test */
    public function it_resolves_default_body(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame('', $event->getBody());
    }

    /** @test */
    public function it_resolves_body(): void
    {
        $expectedData = ['body' => $expectedBody = 'foo', 'isBase64Encoded' => false];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedBody, $event->getBody());
    }

    /** @test */
    public function it_resolves_encoded_body(): void
    {
        $expectedData = ['body' => base64_encode($expectedBody = 'foo'), 'isBase64Encoded' => true];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedBody, $event->getBody());
    }

    /** @test */
    public function it_resolves_default_source_ip(): void
    {
        $expectedData = [];

        $event = new HttpEvent($expectedData);

        self::assertSame('127.0.0.1', $event->getSourceIp());
    }

    /** @test */
    public function it_resolves_source_ip_from_payload_version_1(): void
    {
        $expectedData = ['requestContext' => ['identity' => ['sourceIp' => $expectedSourceIp = '127.0.0.2']]];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedSourceIp, $event->getSourceIp());
    }

    /** @test */
    public function it_resolves_source_ip_from_payload_version_2(): void
    {
        $expectedData = ['requestContext' => ['http' => ['sourceIp' => $expectedSourceIp = '127.0.0.2']]];

        $event = new HttpEvent($expectedData);

        self::assertSame($expectedSourceIp, $event->getSourceIp());
    }
}
