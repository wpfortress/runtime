<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\FastCGIProcessClientContract;
use WPFortress\Runtime\Contracts\FastCGIRequestFactoryContract;
use WPFortress\Runtime\Contracts\InvocationContextContract;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;
use WPFortress\Runtime\Contracts\InvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\WordPressHandler;

final class WordPressHandlerTest extends TestCase
{
    /** @test */
    public function it_should_handle_http_events(): void
    {
        $tmpDir = sys_get_temp_dir();

        $stubbedFastCGIRequestFactory = $this->createStub(FastCGIRequestFactoryContract::class);
        $stubbedFastCGIProcessClient = $this->createStub(FastCGIProcessClientContract::class);
        $stubbedInvocationEvent = $this->createStub(InvocationHttpEventContract::class);
        $stubbedHttpResponseFactory = $this->createStub(InvocationHttpResponseFactoryContract::class);
        $mockedInvocation = $this->createMock(InvocationContract::class);

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');

        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedInvocationEvent);

        $handler = new WordPressHandler(
            $stubbedFastCGIRequestFactory,
            $stubbedFastCGIProcessClient,
            $stubbedHttpResponseFactory,
            $tmpDir,
        );

        $shouldHandle = $handler->shouldHandle($mockedInvocation);

        unlink($tmpDir . '/index.php');
        unlink($tmpDir . '/wp-config.php');

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_creates_invocation_response_for_index_file(): void
    {
        $tmpDir = sys_get_temp_dir();

        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);
        $stubbedInvocationResponse = $this->createStub(InvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(InvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedInvocationContext = $this->createMock(InvocationContextContract::class);
        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);
        $mockedInvocation = $this->createMock(InvocationContract::class);

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');

        $mockedInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedInvocationContext);

        $mockedInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedInvocation
            ->expects(self::exactly(4))
            ->method('getEvent')
            ->willReturn($mockedInvocationEvent);

        $mockedInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::identicalTo($mockedInvocation), $tmpDir . '/index.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::identicalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::identicalTo($mockedInvocation), self::identicalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedInvocation);

        unlink($tmpDir . '/index.php');
        unlink($tmpDir . '/wp-config.php');

        self::assertSame($stubbedInvocationResponse, $response);
    }

    /** @test */
    public function it_creates_invocation_response_for_folder_with_index_file(): void
    {
        $tmpDir = sys_get_temp_dir();

        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);
        $stubbedInvocationResponse = $this->createStub(InvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(InvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedInvocationContext = $this->createMock(InvocationContextContract::class);
        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);
        $mockedInvocation = $this->createMock(InvocationContract::class);

        mkdir($tmpDir . '/foo');

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');
        touch($tmpDir . '/foo/index.php');

        $mockedInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedInvocationContext);

        $mockedInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedInvocation
            ->expects(self::exactly(4))
            ->method('getEvent')
            ->willReturn($mockedInvocationEvent);

        $mockedInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo/');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::identicalTo($mockedInvocation), $tmpDir . '/foo/index.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::identicalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::identicalTo($mockedInvocation), self::identicalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedInvocation);

        unlink($tmpDir . '/index.php');
        unlink($tmpDir . '/wp-config.php');
        unlink($tmpDir . '/foo/index.php');

        rmdir($tmpDir . '/foo');

        self::assertSame($stubbedInvocationResponse, $response);
    }

    /** @test */
    public function it_creates_invocation_response_for_subdirectory_multisite_admin(): void
    {
        $tmpDir = sys_get_temp_dir();

        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);
        $stubbedInvocationResponse = $this->createStub(InvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(InvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedInvocationContext = $this->createMock(InvocationContextContract::class);
        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);
        $mockedInvocation = $this->createMock(InvocationContract::class);

        mkdir($tmpDir . '/wp-admin');

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');
        touch($tmpDir . '/wp-admin/index.php');

        file_put_contents($tmpDir . '/wp-config.php', 'define(\'MULTISITE\', true);');

        $mockedInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedInvocationContext);

        $mockedInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedInvocation
            ->expects(self::exactly(4))
            ->method('getEvent')
            ->willReturn($mockedInvocationEvent);

        $mockedInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo/wp-admin/');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::identicalTo($mockedInvocation), $tmpDir . '/wp-admin/index.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::identicalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::identicalTo($mockedInvocation), self::identicalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedInvocation);

        unlink($tmpDir . '/index.php');
        unlink($tmpDir . '/wp-config.php');
        unlink($tmpDir . '/wp-admin/index.php');

        rmdir($tmpDir . '/wp-admin');

        self::assertSame($stubbedInvocationResponse, $response);
    }

    /** @test */
    public function it_creates_invocation_response_for_subdirectory_multisite_login(): void
    {
        $tmpDir = sys_get_temp_dir();

        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);
        $stubbedInvocationResponse = $this->createStub(InvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(InvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedInvocationContext = $this->createMock(InvocationContextContract::class);
        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);
        $mockedInvocation = $this->createMock(InvocationContract::class);

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');
        touch($tmpDir . '/wp-login.php');

        file_put_contents($tmpDir . '/wp-config.php', 'define(\'MULTISITE\', true);');

        $mockedInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedInvocationContext);

        $mockedInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedInvocation
            ->expects(self::exactly(4))
            ->method('getEvent')
            ->willReturn($mockedInvocationEvent);

        $mockedInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo/wp-login.php');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::identicalTo($mockedInvocation), $tmpDir . '/wp-login.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::identicalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::identicalTo($mockedInvocation), self::identicalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedInvocation);

        unlink($tmpDir . '/index.php');
        unlink($tmpDir . '/wp-config.php');
        unlink($tmpDir . '/wp-login.php');

        self::assertSame($stubbedInvocationResponse, $response);
    }
}