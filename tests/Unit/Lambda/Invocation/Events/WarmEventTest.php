<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\WarmEvent;

final class WarmEventTest extends TestCase
{
    /** @test */
    public function it_should_handle_given_data(): void
    {
        $shouldHandle = WarmEvent::shouldHandle([
            'warm' => 10,
        ]);

        self::assertTrue($shouldHandle);

        $shouldHandle = WarmEvent::shouldHandle([
            'requestContext' => [],
        ]);

        self::assertFalse($shouldHandle);
    }

    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['warm' => $expectedConcurrency = 10];

        $event = WarmEvent::fromResponseData($expectedData);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertSame($expectedConcurrency, $event->getConcurrency());
    }
}
