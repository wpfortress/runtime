<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationHandlerBusContract
{
    public function handle(LambdaInvocationContract $invocation): InvocationHandlerContract;
}
