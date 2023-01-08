<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\PingResponse;

final class PingResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_response(): void
    {
        $response = new PingResponse();
        $result = $response->jsonSerialize();

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertSame(['Pong'], $result);
    }
}
