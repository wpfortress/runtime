<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEvent;

final class PingEventTest extends TestCase
{
    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['ping' => true];

        $event = new PingEvent($expectedData);

        self::assertInstanceOf(InvocationEvent::class, $event);
        self::assertSame($expectedData, $event->getData());
    }
}
