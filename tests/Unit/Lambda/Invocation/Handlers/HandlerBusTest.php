<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerBusContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\HandlerBus;

final class HandlerBusTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_invocation_handler_collection_contract(): void
    {
        $handlerCollection = new HandlerBus([]);

        self::assertInstanceOf(LambdaInvocationHandlerBusContract::class, $handlerCollection);
    }

    /** @test */
    public function it_throws_exception_for_unhandled_invocation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unhandled Lambda invocation.');

        $stubbedInvocation = $this->createStub(LambdaInvocationContract::class);

        $handlerCollection = new HandlerBus([]);
        $handlerCollection->handle($stubbedInvocation);
    }

    /** @test */
    public function it_picks_handler_for_given_invocation(): void
    {
        $stubbedInvocation = $this->createStub(LambdaInvocationContract::class);
        $mockedHandler = $this->createMock(LambdaInvocationHandlerContract::class);

        $mockedHandler
            ->expects(self::once())
            ->method('shouldHandle')
            ->with($stubbedInvocation)
            ->willReturn(true);

        $handlerCollection = new HandlerBus([$mockedHandler]);
        $handler = $handlerCollection->handle($stubbedInvocation);

        self::assertSame($mockedHandler, $handler);
    }
}
