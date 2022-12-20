<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEvent;

final class CliEventTest extends TestCase
{
    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['cli' => $expectedCommand = 'foo'];

        $event = new CliEvent($expectedData);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertSame($expectedData, $event->getData());
        self::assertSame($expectedCommand, $event->getCommand());
    }
}
