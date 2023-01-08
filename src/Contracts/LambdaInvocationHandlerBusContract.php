<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface LambdaInvocationHandlerBusContract
{
    public function handle(LambdaInvocationContract $invocation): InvocationHandlerContract;
}
