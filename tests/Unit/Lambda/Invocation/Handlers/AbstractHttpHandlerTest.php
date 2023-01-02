<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;
use WPFortress\Runtime\Contracts\InvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\AbstractHttpHandler;

final class AbstractHttpHandlerTest extends TestCase
{
    /** @test */
    public function it_should_handle_http_events(): void
    {
        $stubbedInvocationEvent = $this->createStub(InvocationHttpEventContract::class);

        $stubbedHttpResponseFactory = $this->createStub(InvocationHttpResponseFactoryContract::class);

        $mockedInvocation = $this->createMock(InvocationContract::class);
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
        $mockedInvocationEvent = $this->createMock(InvocationHttpEventContract::class);
        $mockedInvocationEvent
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('/tmp');

        $mockedInvocation = $this->createMock(InvocationContract::class);
        $mockedInvocation
            ->method('getEvent')
            ->willReturn($mockedInvocationEvent);

        $stubbedHttpResponseFactory = $this->createStub(InvocationHttpResponseFactoryContract::class);

        $stubbedResponse = $this->createStub(InvocationResponseContract::class);

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
