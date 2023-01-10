<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\FastCGIProcessClientContract;
use WPFortress\Runtime\Contracts\FastCGIRequestFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\WordPressHandler;

final class WordPressHandlerTest extends TestCase
{
    /** @test */
    public function it_should_handle_http_events(): void
    {
        $tmpDir = sys_get_temp_dir();

        $stubbedFastCGIRequestFactory = $this->createStub(FastCGIRequestFactoryContract::class);
        $stubbedFastCGIProcessClient = $this->createStub(FastCGIProcessClientContract::class);
        $stubbedLambdaInvocationHttpEvent = $this->createStub(LambdaInvocationHttpEventContract::class);
        $stubbedLambdaInvocationHttpResponseFactory = $this->createStub(
            LambdaInvocationHttpResponseFactoryContract::class
        );
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedLambdaInvocationHttpEvent);

        $handler = new WordPressHandler(
            $stubbedFastCGIRequestFactory,
            $stubbedFastCGIProcessClient,
            $stubbedLambdaInvocationHttpResponseFactory,
            $tmpDir,
        );

        $shouldHandle = $handler->shouldHandle($mockedLambdaInvocation);

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
        $stubbedInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(LambdaInvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedLambdaInvocationEvent = $this->createMock(LambdaInvocationHttpEventContract::class);
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);
        $mockedLambdaInvocation
            ->expects(self::atLeast(2))
            ->method('getEvent')
            ->willReturn($mockedLambdaInvocationEvent);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedLambdaInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::equalTo($mockedLambdaInvocation), $tmpDir . '/index.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::equalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::equalTo($mockedLambdaInvocation), self::equalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedLambdaInvocation);

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
        $stubbedInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(LambdaInvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedLambdaInvocationEvent = $this->createMock(LambdaInvocationHttpEventContract::class);
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);

        mkdir($tmpDir . '/foo');

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');
        touch($tmpDir . '/foo/index.php');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);
        $mockedLambdaInvocation
            ->expects(self::atLeast(2))
            ->method('getEvent')
            ->willReturn($mockedLambdaInvocationEvent);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedLambdaInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo/');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::equalTo($mockedLambdaInvocation), $tmpDir . '/foo/index.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::equalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::equalTo($mockedLambdaInvocation), self::equalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedLambdaInvocation);

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
        $stubbedInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(LambdaInvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedLambdaInvocationEvent = $this->createMock(LambdaInvocationHttpEventContract::class);
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);

        mkdir($tmpDir . '/wp-admin');

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');
        touch($tmpDir . '/wp-admin/index.php');

        file_put_contents($tmpDir . '/wp-config.php', 'define(\'MULTISITE\', true);');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);
        $mockedLambdaInvocation
            ->expects(self::atLeast(2))
            ->method('getEvent')
            ->willReturn($mockedLambdaInvocationEvent);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedLambdaInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo/wp-admin/');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::equalTo($mockedLambdaInvocation), $tmpDir . '/wp-admin/index.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::equalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::equalTo($mockedLambdaInvocation), self::equalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedLambdaInvocation);

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
        $stubbedInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(LambdaInvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedLambdaInvocationEvent = $this->createMock(LambdaInvocationHttpEventContract::class);
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);

        touch($tmpDir . '/index.php');
        touch($tmpDir . '/wp-config.php');
        touch($tmpDir . '/wp-login.php');

        file_put_contents($tmpDir . '/wp-config.php', 'define(\'MULTISITE\', true);');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);
        $mockedLambdaInvocation
            ->expects(self::atLeast(2))
            ->method('getEvent')
            ->willReturn($mockedLambdaInvocationEvent);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedLambdaInvocationEvent
            ->expects(self::exactly(2))
            ->method('getPath')
            ->willReturn('foo/wp-login.php');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::equalTo($mockedLambdaInvocation), $tmpDir . '/wp-login.php')
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::equalTo($stubbedFastCGIRequest), 2000)
            ->willReturn($stubbedFastCGIResponse);

        $mockedHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::equalTo($mockedLambdaInvocation), self::equalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedInvocationResponse);

        $handler = new WordPressHandler(
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            $tmpDir,
        );

        $response = $handler->handle($mockedLambdaInvocation);

        unlink($tmpDir . '/index.php');
        unlink($tmpDir . '/wp-config.php');
        unlink($tmpDir . '/wp-login.php');

        self::assertSame($stubbedInvocationResponse, $response);
    }
}
