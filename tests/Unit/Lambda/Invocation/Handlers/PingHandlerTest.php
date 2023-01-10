<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEvent;
use WPFortress\Runtime\Lambda\Invocation\Handlers\PingHandler;
use WPFortress\Runtime\Lambda\Invocation\Responses\PingResponse;

final class PingHandlerTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_invocation_handler_contract(): void
    {
        $handler = new PingHandler();

        self::assertInstanceOf(LambdaInvocationHandlerContract::class, $handler);
    }

    /** @test */
    public function it_should_handle_cli_events(): void
    {
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $lambdaInvocationEvent = new PingEvent();

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($lambdaInvocationEvent);

        $handler = new PingHandler();
        $shouldHandle = $handler->shouldHandle($mockedLambdaInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_handles_ping_event(): void
    {
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $lambdaInvocationEvent = new PingEvent();

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($lambdaInvocationEvent);

        $handler = new PingHandler();
        $response = $handler->handle($mockedLambdaInvocation);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(PingResponse::class, $response);
    }
}
