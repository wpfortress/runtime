<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContextFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationEventFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationFactoryContract;
use WPFortress\Runtime\Lambda\Invocation\InvocationFactory;

final class InvocationFactoryTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_invocation_factory_contract(): void
    {
        $stubbedLambdaInvocationContextFactory = $this->createStub(LambdaInvocationContextFactoryContract::class);
        $stubbedLambdaInvocationEventFactory = $this->createStub(LambdaInvocationEventFactoryContract::class);

        $invocationFactory = new InvocationFactory(
            $stubbedLambdaInvocationContextFactory,
            $stubbedLambdaInvocationEventFactory
        );

        self::assertInstanceOf(LambdaInvocationFactoryContract::class, $invocationFactory);
    }

    /** @test */
    public function it_makes_invocation_from_response(): void
    {
        $mockedLambdaInvocationContextFactory = $this->createMock(LambdaInvocationContextFactoryContract::class);
        $stubbedLambdaInvocationContext = $this->createStub(LambdaInvocationContextContract::class);
        $mockedLambdaInvocationEventFactory = $this->createMock(LambdaInvocationEventFactoryContract::class);
        $stubbedLambdaInvocationEvent = $this->createStub(LambdaInvocationEventContract::class);
        $mockedResponse = $this->createMock(ResponseInterface::class);

        $mockedLambdaInvocationContextFactory
            ->expects(self::once())
            ->method('make')
            ->with([])
            ->willReturn($stubbedLambdaInvocationContext);

        $mockedLambdaInvocationEventFactory
            ->expects(self::once())
            ->method('make')
            ->with([])
            ->willReturn($stubbedLambdaInvocationEvent);

        $mockedResponse
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([]);
        $mockedResponse
            ->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $invocationFactory = new InvocationFactory(
            $mockedLambdaInvocationContextFactory,
            $mockedLambdaInvocationEventFactory
        );
        $invocation = $invocationFactory->make($mockedResponse);

        self::assertInstanceOf(LambdaInvocationContract::class, $invocation);
        self::assertSame($stubbedLambdaInvocationContext, $invocation->getContext());
        self::assertSame($stubbedLambdaInvocationEvent, $invocation->getEvent());
    }
}
