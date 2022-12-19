<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationEvent
{
    /** @return array<string, mixed> */
    public function getData(): array;
}
