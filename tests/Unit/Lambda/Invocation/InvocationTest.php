<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\Lambda\Invocation\Invocation;

final class InvocationTest extends TestCase
{
    /** @test */
    public function it_forms_correct_invocation(): void
    {
        $stubbedInvocationContext = $this->createStub(LambdaInvocationContextContract::class);
        $stubbedInvocationEvent = $this->createStub(LambdaInvocationEventContract::class);

        $invocation = new Invocation(
            context: $stubbedInvocationContext,
            event: $stubbedInvocationEvent,
        );

        self::assertInstanceOf(LambdaInvocationContract::class, $invocation);
        self::assertSame($stubbedInvocationContext, $invocation->getContext());
        self::assertSame($stubbedInvocationEvent, $invocation->getEvent());
    }
}
