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
use WPFortress\Runtime\Lambda\Invocation\Handlers\AbstractPhpFpmHandler;

final class AbstractPhpFpmHandlerTest extends TestCase
{
    /** @test */
    public function it_should_handle_http_events(): void
    {
        $stubbedFastCGIRequestFactory = $this->createStub(FastCGIRequestFactoryContract::class);
        $stubbedFastCGIProcessClient = $this->createStub(FastCGIProcessClientContract::class);
        $stubbedInvocationEvent = $this->createStub(InvocationHttpEventContract::class);
        $stubbedHttpResponseFactory = $this->createStub(InvocationHttpResponseFactoryContract::class);
        $mockedInvocation = $this->createMock(InvocationContract::class);

        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedInvocationEvent);

        $handler = $this->getMockForAbstractClass(AbstractPhpFpmHandler::class, [
            $stubbedFastCGIRequestFactory,
            $stubbedFastCGIProcessClient,
            $stubbedHttpResponseFactory,
            '/tmp',
        ]);

        $shouldHandle = $handler->shouldHandle($mockedInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_creates_invocation_response(): void
    {
        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);
        $stubbedInvocationResponse = $this->createStub(InvocationResponseContract::class);
        $mockedHttpResponseFactory = $this->createMock(InvocationHttpResponseFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedInvocationContext = $this->createMock(InvocationContextContract::class);
        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);
        $mockedInvocation = $this->createMock(InvocationContract::class);

        $mockedInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedInvocationContext);

        $mockedInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedInvocation
            ->expects(self::atLeast(2))
            ->method('getEvent')
            ->willReturn($mockedInvocationEvent);

        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('foo');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::identicalTo($mockedInvocation))
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

        $handler = $this->getMockForAbstractClass(AbstractPhpFpmHandler::class, [
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedHttpResponseFactory,
            '/',
        ]);

        $response = $handler->handle($mockedInvocation);

        self::assertSame($stubbedInvocationResponse, $response);
    }
}
