<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEvent;

final class PingEventTest extends TestCase
{
    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['ping' => true];

        $event = PingEvent::fromResponseData($expectedData);

        self::assertInstanceOf(InvocationEventContract::class, $event);
    }
}
