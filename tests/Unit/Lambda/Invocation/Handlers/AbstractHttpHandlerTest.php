<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\AbstractHttpHandler;

final class AbstractHttpHandlerTest extends TestCase
{
    /** @test */
    public function it_should_handle_http_events(): void
    {
        $stubbedInvocationEvent = $this->createStub(LambdaInvocationHttpEventContract::class);

        $stubbedHttpResponseFactory = $this->createStub(LambdaInvocationHttpResponseFactoryContract::class);

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedInvocationEvent);

        $handler = $this->getMockForAbstractClass(AbstractHttpHandler::class, [
            $stubbedHttpResponseFactory,
            '/tmp',
        ]);

        $shouldHandle = $handler->shouldHandle($mockedInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_creates_invocation_response(): void
    {
        $mockedInvocationEvent = $this->createMock(LambdaInvocationHttpEventContract::class);
        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('/tmp');

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->method('getEvent')
            ->willReturn($mockedInvocationEvent);

        $stubbedHttpResponseFactory = $this->createStub(LambdaInvocationHttpResponseFactoryContract::class);

        $stubbedResponse = $this->createStub(LambdaInvocationResponseContract::class);

        $handler = $this->getMockForAbstractClass(AbstractHttpHandler::class, [
            $stubbedHttpResponseFactory,
            '/tmp',
        ]);

        $handler
            ->expects(self::once())
            ->method('createInvocationResponse')
            ->with(self::identicalTo($mockedInvocation))
            ->willReturn($stubbedResponse);

        $response = $handler->handle($mockedInvocation);

        self::assertSame($stubbedResponse, $response);
    }
}
