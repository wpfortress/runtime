<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use stdClass;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEvent;
use WPFortress\Runtime\Lambda\Invocation\Handlers\CliHandler;
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
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $lambdaInvocationEvent = new CliEvent(command: 'ls -la');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($lambdaInvocationEvent);

        $handler = new CliHandler();
        $shouldHandle = $handler->shouldHandle($mockedLambdaInvocation);

        self::assertTrue($shouldHandle);
    }

    /** @test */
    public function it_handles_cli_event(): void
    {
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $lambdaInvocationEvent = new CliEvent(command: 'ls -la');
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);

        $mockedLambdaInvocation
            ->expects(self::exactly(2))
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);
        $mockedLambdaInvocation
            ->expects(self::atLeast(1))
            ->method('getEvent')
            ->willReturn($lambdaInvocationEvent);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getRemainingTimeInMs')
            ->willReturn(100);
        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('jsonSerialize')
            ->willReturn(new stdClass());

        $handler = new CliHandler();
        $response = $handler->handle($mockedLambdaInvocation);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(CliResponse::class, $response);
    }
}
