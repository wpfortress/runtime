<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Invocation;

final class InvocationTest extends TestCase
{
    /** @test */
    public function it_forms_correct_invocation(): void
    {
        $stubbedLambdaInvocationContext = $this->createStub(LambdaInvocationContextContract::class);
        $stubbedLambdaInvocationEvent = $this->createStub(LambdaInvocationEventContract::class);

        $invocation = new Invocation(
            context: $stubbedLambdaInvocationContext,
            event: $stubbedLambdaInvocationEvent,
        );

        self::assertInstanceOf(LambdaInvocationContract::class, $invocation);
        self::assertSame($stubbedLambdaInvocationContext, $invocation->getContext());
        self::assertSame($stubbedLambdaInvocationEvent, $invocation->getEvent());
    }
}
