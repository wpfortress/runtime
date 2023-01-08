<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface LambdaInvocationContextFactoryContract
{
    /** @param array<string, list<string>> $headers */
    public function make(array $headers): LambdaInvocationContextContract;
}
