<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerCollectionContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;
use WPFortress\Runtime\Lambda\Invocation\Handlers\HandlerCollection;

final class HandlerCollectionTest extends TestCase
{
    /** @test */
    public function it_implements_invocation_handler_collection_contract(): void
    {
        $handlerCollection = new HandlerCollection([]);

        self::assertInstanceOf(InvocationHandlerCollectionContract::class, $handlerCollection);
    }

    /** @test */
    public function it_throws_exception_for_unhandled_invocation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unhandled Lambda invocation.');

        $stubbedInvocation = $this->createStub(InvocationContract::class);

        $handlerCollection = new HandlerCollection([]);
        $handlerCollection->pickFor($stubbedInvocation);
    }

    /** @test */
    public function it_picks_handler_for_given_invocation(): void
    {
        $stubbedInvocation = $this->createStub(InvocationContract::class);
        $mockedHandler = $this->createMock(InvocationHandlerContract::class);

        $mockedHandler
            ->expects(self::once())
            ->method('shouldHandle')
            ->with($stubbedInvocation)
            ->willReturn(true);

        $handlerCollection = new HandlerCollection([$mockedHandler]);
        $handler = $handlerCollection->pickFor($stubbedInvocation);

        self::assertSame($mockedHandler, $handler);
    }
}
