<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationHttpErrorResponseContract;

final class NotFoundHttpResponse extends AbstractHttpErrorResponse implements InvocationHttpErrorResponseContract
{
    public static function make(string $template = '/opt/templates/error.phtml'): self
    {
        $message = 'Not Found';
        $status = HttpStatus::NOT_FOUND;

        return new self(
            body: self::capture($template, compact('message', 'status')),
            headers: ['Content-Type' => ['text/html; charset=utf-8']],
            status: $status,
        );
    }
}
