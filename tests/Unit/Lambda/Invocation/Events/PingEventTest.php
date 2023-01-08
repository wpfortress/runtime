<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Events;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEventLambda;

final class PingEventTest extends TestCase
{
    /** @test */
    public function it_should_handle_given_data(): void
    {
        $shouldHandle = PingEventLambda::shouldHandle([
            'ping' => true,
        ]);

        self::assertTrue($shouldHandle);

        $shouldHandle = PingEventLambda::shouldHandle([
            'requestContext' => [],
        ]);

        self::assertFalse($shouldHandle);
    }

    /** @test */
    public function it_forms_correct_event(): void
    {
        $expectedData = ['ping' => true];

        $event = PingEventLambda::fromResponseData($expectedData);

        self::assertInstanceOf(LambdaInvocationEventContract::class, $event);
    }
}
