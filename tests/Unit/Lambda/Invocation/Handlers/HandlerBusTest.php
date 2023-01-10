<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerBusContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\HandlerBus;

final class HandlerBusTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_invocation_handler_collection_contract(): void
    {
        $handlerBus = new HandlerBus([]);

        self::assertInstanceOf(LambdaInvocationHandlerBusContract::class, $handlerBus);
    }

    /** @test */
    public function it_throws_exception_for_unhandled_invocation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unhandled Lambda invocation.');

        $stubbedLambdaInvocation = $this->createStub(LambdaInvocationContract::class);

        $handlerBus = new HandlerBus([]);
        $handlerBus->handle($stubbedLambdaInvocation);
    }

    /** @test */
    public function it_handles_given_invocation(): void
    {
        $stubbedInvocation = $this->createStub(LambdaInvocationContract::class);
        $mockedLambdaInvocationHandler = $this->createMock(LambdaInvocationHandlerContract::class);
        $stubbedLambdaInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);

        $mockedLambdaInvocationHandler
            ->expects(self::once())
            ->method('shouldHandle')
            ->with(self::equalTo($stubbedInvocation))
            ->willReturn(true);

        $mockedLambdaInvocationHandler
            ->expects(self::once())
            ->method('handle')
            ->with(self::equalTo($stubbedInvocation))
            ->willReturn($stubbedLambdaInvocationResponse);

        $handlerBus = new HandlerBus([$mockedLambdaInvocationHandler]);
        $invocationResponse = $handlerBus->handle($stubbedInvocation);

        self::assertSame($stubbedLambdaInvocationResponse, $invocationResponse);
    }
}
