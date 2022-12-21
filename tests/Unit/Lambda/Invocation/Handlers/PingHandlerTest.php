<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Context\Context;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEvent;
use WPFortress\Runtime\Lambda\Invocation\Handlers\PingHandler;
use WPFortress\Runtime\Lambda\Invocation\Invocation;
use WPFortress\Runtime\Lambda\Invocation\Responses\PingResponse;

final class PingHandlerTest extends TestCase
{
    /** @test */
    public function it_implements_invocation_handler_contract(): void
    {
        $handler = new PingHandler();

        self::assertInstanceOf(InvocationHandlerContract::class, $handler);
    }

    /** @test */
    public function it_should_handle_cli_events(): void
    {
        $invocationEvent = new PingEvent([]);

        $mockedInvocation = $this->createMock(InvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($invocationEvent);

        $handler = new PingHandler();
        $shouldHandle = $handler->shouldHandle($mockedInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_handles_ping_event(): void
    {
        $invocationEvent = new PingEvent([]);

        $invocationContext = new Context(
            awsRequestId: '8476a536-e9f4-11e8-9739-2dfe598c3fcd',
            deadlineInMs: intval(microtime(true) * 1000) + 100,
            remainingTimeInMs: 100,
            invokedFunctionArn: 'arn:aws:lambda:us-east-2:123456789012:function:custom-runtime',
            traceId: 'Root=1-5bef4de7-ad49b0e87f6ef6c87fc2e700;Parent=9a9197af755a6419;Sampled=1',
        );

        $invocation = new Invocation(
            context: $invocationContext,
            event: $invocationEvent,
        );

        $handler = new PingHandler();
        $response = $handler->handle($invocation);

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(PingResponse::class, $response);
    }
}