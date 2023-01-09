<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Context\Context;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEvent;
use WPFortress\Runtime\Lambda\Invocation\Handlers\CliHandler;
use WPFortress\Runtime\Lambda\Invocation\Invocation;
use WPFortress\Runtime\Lambda\Invocation\Responses\CliResponse;

final class CliHandlerTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_invocation_handler_contract(): void
    {
        $handler = new CliHandler();

        self::assertInstanceOf(LambdaInvocationHandlerContract::class, $handler);
    }

    /** @test */
    public function it_should_handle_cli_events(): void
    {
        $invocationEvent = new CliEvent(command: 'ls -la');

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($invocationEvent);

        $handler = new CliHandler();
        $shouldHandle = $handler->shouldHandle($mockedInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_handles_successful_cli_event(): void
    {
        $invocationEvent = new CliEvent(command: 'ls -la');

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

        $handler = new CliHandler();
        $response = $handler->handle($invocation);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(CliResponse::class, $response);
    }

    /** @test */
    public function it_handles_unsuccessful_cli_event(): void
    {
        $invocationEvent = new CliEvent(command: 'foo');

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

        $handler = new CliHandler();
        $response = $handler->handle($invocation);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(CliResponse::class, $response);
    }
}
