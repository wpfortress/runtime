<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationHandlerContract
{
    public function shouldHandle(InvocationContract $invocation): bool;

    public function handle(InvocationContract $invocation): InvocationResponseContract;
}
