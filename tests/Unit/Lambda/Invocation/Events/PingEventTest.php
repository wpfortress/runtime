<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEvent;

final class PingEventTest extends TestCase
{
    /** @test */
    public function it_should_handle_given_data(): void
    {
        $shouldHandle = PingEvent::shouldHandle([
            'ping' => true,
        ]);

        self::assertTrue($shouldHandle);

        $shouldHandle = PingEvent::shouldHandle([
            'requestContext' => [],
        ]);

        self::assertFalse($shouldHandle);
    }

    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['ping' => true];

        $event = PingEvent::fromResponseData($expectedData);

        self::assertInstanceOf(LambdaInvocationEventContract::class, $event);
    }
}
