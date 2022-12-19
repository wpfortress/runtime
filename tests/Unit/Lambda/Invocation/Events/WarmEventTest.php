<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\WarmEvent;

final class WarmEventTest extends TestCase
{
    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['warm' => $expectedConcurrency = 10];

        $event = new WarmEvent($expectedData);

        self::assertInstanceOf(InvocationEvent::class, $event);
        self::assertSame($expectedData, $event->getData());
        self::assertSame($expectedConcurrency, $event->getConcurrency());
    }
}
