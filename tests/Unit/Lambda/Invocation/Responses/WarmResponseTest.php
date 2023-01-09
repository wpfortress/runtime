<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\WarmResponse;

final class WarmResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_response(): void
    {
        $response = new WarmResponse();
        $result = $response->jsonSerialize();

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertSame(['Lambda is warm'], $result);
    }
}
