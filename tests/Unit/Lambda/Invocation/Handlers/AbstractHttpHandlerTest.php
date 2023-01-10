<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\AbstractHttpHandler;

final class AbstractHttpHandlerTest extends TestCase
{
    /** @test */
    public function it_should_handle_http_events(): void
    {
        $stubbedLambdaInvocationHttpResponseFactory = $this->createStub(
            LambdaInvocationHttpResponseFactoryContract::class
        );
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $stubbedLambdaInvocationHttpEvent = $this->createStub(LambdaInvocationHttpEventContract::class);

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedLambdaInvocationHttpEvent);

        $handler = $this->getMockForAbstractClass(AbstractHttpHandler::class, [
            $stubbedLambdaInvocationHttpResponseFactory,
            '/tmp',
        ]);

        $shouldHandle = $handler->shouldHandle($mockedLambdaInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_creates_invocation_response(): void
    {
        $stubbedLambdaInvocationHttpResponseFactory = $this->createStub(
            LambdaInvocationHttpResponseFactoryContract::class
        );
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedLambdaInvocationEvent = $this->createMock(LambdaInvocationHttpEventContract::class);
        $stubbedLambdaInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);

        $mockedLambdaInvocation
            ->method('getEvent')
            ->willReturn($mockedLambdaInvocationEvent);

        $mockedLambdaInvocationEvent
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('/tmp');

        $handler = $this->getMockForAbstractClass(AbstractHttpHandler::class, [
            $stubbedLambdaInvocationHttpResponseFactory,
            '/tmp',
        ]);

        $handler
            ->expects(self::once())
            ->method('createInvocationResponse')
            ->with(self::equalTo($mockedLambdaInvocation))
            ->willReturn($stubbedLambdaInvocationResponse);

        $response = $handler->handle($mockedLambdaInvocation);

        self::assertSame($stubbedLambdaInvocationResponse, $response);
    }
}
