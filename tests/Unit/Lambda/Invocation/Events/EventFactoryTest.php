<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\EventFactory;
use WPFortress\Runtime\Lambda\Invocation\Events\HttpEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\WarmEvent;

final class EventFactoryTest extends TestCase
{
    /** @test */
    public function it_throws_exception_for_unknown_event_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown Lambda event type.');

        $data = ['foo' => 'bar'];

        $eventFactory = new EventFactory();
        $eventFactory->make($data);
    }

    /** @test */
    public function it_makes_cli_event_from_response_data(): void
    {
        $data = ['cli' => 'foo'];

        $eventFactory = new EventFactory();
        $event = $eventFactory->make($data);

        self::assertInstanceOf(CliEvent::class, $event);
    }

    /** @test */
    public function it_makes_http_event_from_response_data(): void
    {
        $data = ['requestContext' => []];

        $eventFactory = new EventFactory();
        $event = $eventFactory->make($data);

        self::assertInstanceOf(HttpEvent::class, $event);
    }

    /** @test */
    public function it_makes_ping_event_from_response_data(): void
    {
        $data = ['ping' => true];

        $eventFactory = new EventFactory();
        $event = $eventFactory->make($data);

        self::assertInstanceOf(PingEvent::class, $event);
    }

    /** @test */
    public function it_makes_warm_event_from_response_data(): void
    {
        $data = ['warm' => 10];

        $eventFactory = new EventFactory();
        $event = $eventFactory->make($data);

        self::assertInstanceOf(WarmEvent::class, $event);
    }
}
