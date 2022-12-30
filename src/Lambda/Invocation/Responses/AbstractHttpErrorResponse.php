<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use Throwable;

abstract class AbstractHttpErrorResponse
{
    /** @param array<string, mixed> $data */
    protected static function capture(string $template, array $data): string
    {
        extract($data, EXTR_SKIP);

        ob_start();

        try {
            include $template;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return (string)ob_get_clean();
    }

    /** @param array<string, list<string>> $headers */
    public function __construct(
        protected string $body,
        protected array $headers,
        protected int $status,
    ) {
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /** @return array<string, list<string>> */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
