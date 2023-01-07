<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use AsyncAws\Lambda\LambdaClient;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Context\Context;
use WPFortress\Runtime\Lambda\Invocation\Events\WarmEvent;
use WPFortress\Runtime\Lambda\Invocation\Handlers\WarmHandler;
use WPFortress\Runtime\Lambda\Invocation\Invocation;
use WPFortress\Runtime\Lambda\Invocation\Responses\WarmResponse;

final class WarmHandlerTest extends TestCase
{
    /** @test */
    public function it_implements_invocation_handler_contract(): void
    {
        $stubbedLambdaClient = $this->createStub(LambdaClient::class);
        $lambdaFunctionName = 'foo';

        $handler = new WarmHandler($stubbedLambdaClient, $lambdaFunctionName);

        self::assertInstanceOf(InvocationHandlerContract::class, $handler);
    }

    /** @test */
    public function it_should_handle_warm_events(): void
    {
        $stubbedLambdaClient = $this->createStub(LambdaClient::class);
        $lambdaFunctionName = 'foo';

        $invocationEvent = new WarmEvent(concurrency: 5);

        $mockedInvocation = $this->createMock(InvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($invocationEvent);

        $handler = new WarmHandler($stubbedLambdaClient, $lambdaFunctionName);
        $shouldHandle = $handler->shouldHandle($mockedInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_handles_warm_event_when_concurrency_is_one(): void
    {
        $mockedLambdaClient = $this->createMock(LambdaClient::class);
        $mockedLambdaClient
            ->expects(self::never())
            ->method('invoke');

        $lambdaFunctionName = 'foo';

        $invocationEvent = new WarmEvent(concurrency: 1);

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

        $handler = new WarmHandler($mockedLambdaClient, $lambdaFunctionName);
        $response = $handler->handle($invocation);

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(WarmResponse::class, $response);
    }

    /** @test */
    public function it_handles_warm_event_when_concurrency_greater_than_one(): void
    {
        $mockedLambdaClient = $this->createMock(LambdaClient::class);
        $mockedLambdaClient
            ->expects(self::exactly(5))
            ->method('invoke');

        $lambdaFunctionName = 'foo';

        $invocationEvent = new WarmEvent(concurrency: 5);

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

        $handler = new WarmHandler($mockedLambdaClient, $lambdaFunctionName);
        $response = $handler->handle($invocation);

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertInstanceOf(WarmResponse::class, $response);
    }
}
