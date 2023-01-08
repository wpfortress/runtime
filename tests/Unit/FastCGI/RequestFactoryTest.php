<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\FastCGI;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WPFortress\Runtime\Contracts\FastCGIRequestFactoryContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\FastCGI\RequestFactory;
use WPFortress\Runtime\Lambda\Invocation\Invocation;

final class RequestFactoryTest extends TestCase
{
    use PHPMock;

    /** @test */
    public function it_implements_fast_cgi_request_factory_contract(): void
    {
        $requestFactory = new RequestFactory();

        self::assertInstanceOf(FastCGIRequestFactoryContract::class, $requestFactory);
    }

    /** @test */
    public function it_makes_request_with_default_values(): void
    {
        $namespace = (new ReflectionClass(RequestFactory::class))->getNamespaceName();

        $mockedGetcwd = $this->getFunctionMock($namespace, 'getcwd');
        $mockedGetcwd
            ->expects(self::once())
            ->willReturn('/tmp');

        $mockedTime = $this->getFunctionMock($namespace, 'time');
        $mockedTime
            ->expects(self::once())
            ->willReturn(1672137475);

        $mockedMicrotime = $this->getFunctionMock($namespace, 'microtime');
        $mockedMicrotime
            ->expects(self::once())
            ->willReturn(1672137475.392833);

        $mockedInvocationContext = $this->createMock(LambdaInvocationContextContract::class);

        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getRequestMethod')
            ->willReturn('GET');

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('/');

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getQueryString')
            ->willReturn('');

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([]);

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getBody')
            ->willReturn('');

        $invocation = new Invocation(
            context: $mockedInvocationContext,
            event: $mockedInvocationEvent,
        );

        $requestFactory = new RequestFactory();
        $request = $requestFactory->make($invocation, '/tmp/foo.php');

        self::assertInstanceOf(ProvidesRequestData::class, $request);
        self::assertSame('', $request->getContent());
        self::assertSame([
            'REQUEST_TIME' => 1672137475,
            'REQUEST_TIME_FLOAT' => 1672137475.392833,
            'DOCUMENT_ROOT' => '/tmp',
            'PATH_INFO' => '',
            'PHP_SELF' => '/foo.php',
            'SCRIPT_NAME' => '/foo.php',
            'QUERY_STRING' => '',
            'LAMBDA_INVOCATION_CONTEXT' => '{}',
            'HTTP_HOST' => 'localhost',
            'GATEWAY_INTERFACE' => 'FastCGI/1.0',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'SCRIPT_FILENAME' => '/tmp/foo.php',
            'SERVER_SOFTWARE' => 'WPFortress',
            'REMOTE_ADDR' => '127.0.0.1',
            'REMOTE_PORT' => 80,
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => 80,
            'SERVER_NAME' => 'localhost',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => 0,
        ], $request->getParams());
    }

    /** @test */
    public function it_makes_request_with_custom_values(): void
    {
        $namespace = (new ReflectionClass(RequestFactory::class))->getNamespaceName();

        $mockedGetcwd = $this->getFunctionMock($namespace, 'getcwd');
        $mockedGetcwd
            ->expects(self::once())
            ->willReturn('/tmp');

        $mockedTime = $this->getFunctionMock($namespace, 'time');
        $mockedTime
            ->expects(self::once())
            ->willReturn(1672137475);

        $mockedMicrotime = $this->getFunctionMock($namespace, 'microtime');
        $mockedMicrotime
            ->expects(self::once())
            ->willReturn(1672137475.392833);

        $mockedInvocationContext = $this->createMock(LambdaInvocationContextContract::class);

        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getRequestMethod')
            ->willReturn('GET');

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('/foo');

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getQueryString')
            ->willReturn('foo=bar');

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([
                'content-type' => ['text/html'],
                'content-length' => [3],
                'host' => ['foo.bar'],
                'x-forwarded-host' => ['foo.bar'],
                'x-forwarded-port' => [80],
                'x-forwarded-for' => ['127.0.0.2'],
                'x-forwarded-proto' => ['https'],
            ]);

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getBody')
            ->willReturn('foo');

        $invocation = new Invocation(
            context: $mockedInvocationContext,
            event: $mockedInvocationEvent,
        );

        $requestFactory = new RequestFactory();
        $request = $requestFactory->make($invocation, '/tmp/foo.php');

        self::assertInstanceOf(ProvidesRequestData::class, $request);
        self::assertSame('foo', $request->getContent());
        self::assertSame([
            'REQUEST_TIME' => 1672137475,
            'REQUEST_TIME_FLOAT' => 1672137475.392833,
            'DOCUMENT_ROOT' => '/tmp',
            'PATH_INFO' => '',
            'PHP_SELF' => '/foo.php/foo',
            'SCRIPT_NAME' => '/foo.php',
            'QUERY_STRING' => 'foo=bar',
            'LAMBDA_INVOCATION_CONTEXT' => '{}',
            'HTTPS' => 'on',
            'HTTP_CONTENT_TYPE' => 'text/html',
            'HTTP_CONTENT_LENGTH' => 3,
            'HTTP_HOST' => 'foo.bar',
            'HTTP_X_FORWARDED_HOST' => 'foo.bar',
            'HTTP_X_FORWARDED_PORT' => 80,
            'HTTP_X_FORWARDED_FOR' => '127.0.0.2',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'GATEWAY_INTERFACE' => 'FastCGI/1.0',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo?foo=bar',
            'SCRIPT_FILENAME' => '/tmp/foo.php',
            'SERVER_SOFTWARE' => 'WPFortress',
            'REMOTE_ADDR' => '127.0.0.2',
            'REMOTE_PORT' => 80,
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => 80,
            'SERVER_NAME' => 'foo.bar',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => 3,
        ], $request->getParams());
    }
}
