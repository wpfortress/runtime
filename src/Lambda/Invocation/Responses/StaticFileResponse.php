<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use Symfony\Component\Mime\MimeTypes;
use WPFortress\Runtime\Contracts\InvocationStaticFileResponseContract;

final class StaticFileResponse implements InvocationStaticFileResponseContract
{
    public static function fromFilename(string $filename): self
    {
        $contents = file_get_contents($filename);
        if (!is_string($contents)) {
            throw new \RuntimeException("Unable to get the contents of `{$filename}`.");
        }

        $contentType = (new MimeTypes())->guessMimeType($filename);

        return new self(
            body: $contents,
            headers: ['Content-Type' => [$contentType ?? 'text/plain']],
        );
    }

    /** @param array<string, list<string>> $headers */
    public function __construct(
        private string $body,
        private array $headers,
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
}
