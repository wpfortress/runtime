<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

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

        $stubbedInvocation = $this->createStub(LambdaInvocationContract::class);

        $handlerBus = new HandlerBus([]);
        $handlerBus->handle($stubbedInvocation);
    }

    /** @test */
    public function it_handles_given_invocation(): void
    {
        $stubbedInvocation = $this->createStub(LambdaInvocationContract::class);
        $mockedHandler = $this->createMock(LambdaInvocationHandlerContract::class);
        $stubbedInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);

        $mockedHandler
            ->expects(self::once())
            ->method('shouldHandle')
            ->with(self::equalTo($stubbedInvocation))
            ->willReturn(true);

        $mockedHandler
            ->expects(self::once())
            ->method('handle')
            ->with(self::equalTo($stubbedInvocation))
            ->willReturn($stubbedInvocationResponse);

        $handlerBus = new HandlerBus([$mockedHandler]);
        $invocationResponse = $handlerBus->handle($stubbedInvocation);

        self::assertSame($stubbedInvocationResponse, $invocationResponse);
    }
}
