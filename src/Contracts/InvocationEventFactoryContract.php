<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationEventFactoryContract
{
    /** @param array<string, mixed> $data */
    public function make(array $data): InvocationEventContract;
}
