<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Events;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Lambda\Invocation\Events\EventFactory;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEventLambda;

final class EventFactoryTest extends TestCase
{
    /** @test */
    public function it_throws_exception_for_unknown_event_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown Lambda event type.');

        $data = ['foo' => 'bar'];

        $eventFactory = new EventFactory([]);
        $eventFactory->make($data);
    }

    /** @test */
    public function it_makes_event_from_response_data(): void
    {
        $data = ['ping' => true];

        $eventFactory = new EventFactory([PingEventLambda::class]);
        $event = $eventFactory->make($data);

        self::assertInstanceOf(PingEventLambda::class, $event);
    }
}
