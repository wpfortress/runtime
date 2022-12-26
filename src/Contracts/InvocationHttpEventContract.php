<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationHttpEventContract
{
    public function getMethod(): string;

    public function getPath(): string;

    public function getQueryString(): string;

    /** @return array<string, list<string>> */
    public function getHeaders(): array;

    public function isBase64Encoded(): bool;

    public function getBody(): string;
}
