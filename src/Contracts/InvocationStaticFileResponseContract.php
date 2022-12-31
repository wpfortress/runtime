<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationStaticFileResponseContract
{
    public function getBody(): string;

    /** @return array<string, list<string>> */
    public function getHeaders(): array;
}
