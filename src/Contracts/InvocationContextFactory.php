<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationContextFactory
{
    /** @param array<string, array<array-key, string>> $headers */
    public function make(array $headers): InvocationContext;
}
