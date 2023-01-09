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
        $stubbedInvocationContextFactory = $this->createStub(LambdaInvocationContextFactoryContract::class);
        $stubbedInvocationEventFactory = $this->createStub(LambdaInvocationEventFactoryContract::class);

        $invocationFactory = new InvocationFactory($stubbedInvocationContextFactory, $stubbedInvocationEventFactory);

        self::assertInstanceOf(LambdaInvocationFactoryContract::class, $invocationFactory);
    }

    /** @test */
    public function it_makes_invocation_from_response(): void
    {
        $stubbedInvocationContext = $this->createStub(LambdaInvocationContextContract::class);

        $mockedInvocationContextFactory = $this->createMock(LambdaInvocationContextFactoryContract::class);
        $mockedInvocationContextFactory
            ->expects(self::once())
            ->method('make')
            ->with([])
            ->willReturn($stubbedInvocationContext);

        $stubbedInvocationEvent = $this->createStub(LambdaInvocationEventContract::class);

        $mockedInvocationEventFactory = $this->createMock(LambdaInvocationEventFactoryContract::class);
        $mockedInvocationEventFactory
            ->expects(self::once())
            ->method('make')
            ->with([])
            ->willReturn($stubbedInvocationEvent);

        $mockedResponse = $this->createMock(ResponseInterface::class);
        $mockedResponse
            ->expects(self::once())
            ->method('getHeaders')
            ->willReturn([]);
        $mockedResponse
            ->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $invocationFactory = new InvocationFactory($mockedInvocationContextFactory, $mockedInvocationEventFactory);
        $invocation = $invocationFactory->make($mockedResponse);

        self::assertInstanceOf(LambdaInvocationContract::class, $invocation);
        self::assertSame($stubbedInvocationContext, $invocation->getContext());
        self::assertSame($stubbedInvocationEvent, $invocation->getEvent());
    }
}
