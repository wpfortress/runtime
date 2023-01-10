<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use AsyncAws\Lambda\LambdaClient;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\WarmEvent;
use WPFortress\Runtime\Lambda\Invocation\Handlers\WarmHandler;
use WPFortress\Runtime\Lambda\Invocation\Responses\WarmResponse;

final class WarmHandlerTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_invocation_handler_contract(): void
    {
        $stubbedLambdaClient = $this->createStub(LambdaClient::class);
        $lambdaFunctionName = 'foo';

        $handler = new WarmHandler($stubbedLambdaClient, $lambdaFunctionName);

        self::assertInstanceOf(LambdaInvocationHandlerContract::class, $handler);
    }

    /** @test */
    public function it_should_handle_warm_events(): void
    {
        $stubbedLambdaClient = $this->createStub(LambdaClient::class);
        $lambdaFunctionName = 'foo';
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $lambdaInvocationEvent = new WarmEvent(concurrency: 5);

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($lambdaInvocationEvent);

        $handler = new WarmHandler($stubbedLambdaClient, $lambdaFunctionName);
        $shouldHandle = $handler->shouldHandle($mockedLambdaInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_handles_warm_event_when_concurrency_is_one(): void
    {
        $mockedLambdaClient = $this->createMock(LambdaClient::class);
        $lambdaFunctionName = 'foo';
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $lambdaInvocationEvent = new WarmEvent(concurrency: 1);

        $mockedLambdaClient
            ->expects(self::never())
            ->method('invoke');

        $mockedLambdaInvocation
            ->expects(self::atLeast(1))
            ->method('getEvent')
            ->willReturn($lambdaInvocationEvent);

        $handler = new WarmHandler($mockedLambdaClient, $lambdaFunctionName);
        $response = $handler->handle($mockedLambdaInvocation);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(WarmResponse::class, $response);
    }

    /** @test */
    public function it_handles_warm_event_when_concurrency_greater_than_one(): void
    {
        $mockedLambdaClient = $this->createMock(LambdaClient::class);
        $lambdaFunctionName = 'foo';
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $lambdaInvocationEvent = new WarmEvent(concurrency: 5);

        $mockedLambdaClient
            ->expects(self::exactly(5))
            ->method('invoke');

        $mockedLambdaInvocation
            ->expects(self::atLeast(1))
            ->method('getEvent')
            ->willReturn($lambdaInvocationEvent);

        $handler = new WarmHandler($mockedLambdaClient, $lambdaFunctionName);
        $response = $handler->handle($mockedLambdaInvocation);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(WarmResponse::class, $response);
    }
}
