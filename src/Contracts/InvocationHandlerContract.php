<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationHandlerContract
{
    public function shouldHandle(LambdaInvocationContract $invocation): bool;

    public function handle(LambdaInvocationContract $invocation): InvocationResponseContract;
}
