<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEvent;

final class CliEventTest extends TestCase
{
    /** @test */
    public function it_should_handle_given_data(): void
    {
        $shouldHandle = CliEvent::shouldHandle([
            'cli' => 'foo',
        ]);

        self::assertTrue($shouldHandle);

        $shouldHandle = CliEvent::shouldHandle([
            'requestContext' => [],
        ]);

        self::assertFalse($shouldHandle);
    }

    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['cli' => $expectedCommand = 'foo'];

        $event = CliEvent::fromResponseData($expectedData);

        self::assertInstanceOf(InvocationEventContract::class, $event);
        self::assertSame($expectedCommand, $event->getCommand());
    }
}
