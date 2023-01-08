<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface LambdaInvocationEventFactoryContract
{
    /** @param array<string, scalar|mixed[][]> $data */
    public function make(array $data): LambdaInvocationHttpEventContract|LambdaInvocationEventContract;
}
