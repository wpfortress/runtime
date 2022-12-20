<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationContextContract;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Invocation;

final class InvocationTest extends TestCase
{
    /** @test */
    public function it_forms_correct_invocation(): void
    {
        $stubbedInvocationContext = $this->createStub(InvocationContextContract::class);
        $stubbedInvocationEvent = $this->createStub(InvocationEventContract::class);

        $invocation = new Invocation(
            context: $stubbedInvocationContext,
            event: $stubbedInvocationEvent,
        );

        self::assertInstanceOf(InvocationContract::class, $invocation);
        self::assertSame($stubbedInvocationContext, $invocation->getContext());
        self::assertSame($stubbedInvocationEvent, $invocation->getEvent());
    }
}
