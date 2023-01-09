<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Lambda\Invocation\Responses\AbstractHttpErrorResponse;

final class AbstractHttpErrorResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_http_error_response(): void
    {
        $errorResponse = $this->getMockForAbstractClass(AbstractHttpErrorResponse::class, [
            $expectedBody = 'foo',
            $expectedHeaders = ['Content-Type' => ['text/html; charset=utf-8']],
            $expectedStatus = HttpStatus::NOT_FOUND,
        ]);

        self::assertSame($expectedBody, $errorResponse->getBody());
        self::assertSame($expectedHeaders, $errorResponse->getHeaders());
        self::assertSame($expectedStatus, $errorResponse->getStatus());
    }
}
