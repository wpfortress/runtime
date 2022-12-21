<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\WarmResponse;

final class WarmResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_response(): void
    {
        $response = new WarmResponse();
        $result = $response->toApiGatewayFormat();

        self::assertInstanceOf(InvocationResponseContract::class, $response);
        self::assertSame(['Lambda is warm'], $result);
    }
}
