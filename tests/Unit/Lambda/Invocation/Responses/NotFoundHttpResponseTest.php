<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Lambda\Invocation\Responses\NotFoundHttpResponse;

final class NotFoundHttpResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_not_found_response(): void
    {
        $template = __DIR__ . '/../../../../../templates/error.phtml';

        $status = HttpStatus::NOT_FOUND;
        $message = 'Not Found';

        ob_start();
        include $template;
        $body = (string)ob_get_clean();

        $errorResponse = NotFoundHttpResponse::make($template);

        self::assertSame($body, $errorResponse->getBody());
        self::assertSame(['Content-Type' => ['text/html; charset=utf-8']], $errorResponse->getHeaders());
        self::assertSame($status, $errorResponse->getStatus());
    }
}
