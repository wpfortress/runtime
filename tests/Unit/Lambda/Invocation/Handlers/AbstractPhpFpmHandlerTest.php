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
use WPFortress\Runtime\Lambda\Invocation\Handlers\AbstractPhpFpmHandler;

final class AbstractPhpFpmHandlerTest extends TestCase
{
    /** @test */
    public function it_should_handle_http_events(): void
    {
        $stubbedFastCGIRequestFactory = $this->createStub(FastCGIRequestFactoryContract::class);
        $stubbedFastCGIProcessClient = $this->createStub(FastCGIProcessClientContract::class);
        $stubbedLambdaInvocationHttpResponseFactory = $this->createStub(
            LambdaInvocationHttpResponseFactoryContract::class
        );
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $stubbedLambdaInvocationEvent = $this->createStub(LambdaInvocationHttpEventContract::class);

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedLambdaInvocationEvent);

        $handler = $this->getMockForAbstractClass(AbstractPhpFpmHandler::class, [
            $stubbedFastCGIRequestFactory,
            $stubbedFastCGIProcessClient,
            $stubbedLambdaInvocationHttpResponseFactory,
            '/tmp',
        ]);

        $shouldHandle = $handler->shouldHandle($mockedLambdaInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_creates_invocation_response(): void
    {
        $mockedFastCGIRequestFactory = $this->createMock(FastCGIRequestFactoryContract::class);
        $mockedFastCGIProcessClient = $this->createMock(FastCGIProcessClientContract::class);
        $mockedLambdaInvocationHttpResponseFactory = $this->createMock(
            LambdaInvocationHttpResponseFactoryContract::class
        );
        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);
        $stubbedLambdaInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedLambdaInvocationHttpEvent = $this->createMock(LambdaInvocationHttpEventContract::class);
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);
        $mockedLambdaInvocation
            ->expects(self::atLeast(2))
            ->method('getEvent')
            ->willReturn($mockedLambdaInvocationHttpEvent);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(3000);

        $mockedLambdaInvocationHttpEvent
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('foo');

        $mockedFastCGIRequestFactory
            ->expects(self::once())
            ->method('make')
            ->with(self::equalTo($mockedLambdaInvocation))
            ->willReturn($stubbedFastCGIRequest);

        $mockedFastCGIProcessClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with(self::equalTo($stubbedFastCGIRequest), self::equalTo(2000))
            ->willReturn($stubbedFastCGIResponse);

        $mockedLambdaInvocationHttpResponseFactory
            ->expects(self::once())
            ->method('makeFromFastCGIResponse')
            ->with(self::equalTo($mockedLambdaInvocation), self::equalTo($stubbedFastCGIResponse))
            ->willReturn($stubbedLambdaInvocationResponse);

        $handler = $this->getMockForAbstractClass(AbstractPhpFpmHandler::class, [
            $mockedFastCGIRequestFactory,
            $mockedFastCGIProcessClient,
            $mockedLambdaInvocationHttpResponseFactory,
            '/',
        ]);

        $response = $handler->handle($mockedLambdaInvocation);

        self::assertSame($stubbedLambdaInvocationResponse, $response);
    }
}
