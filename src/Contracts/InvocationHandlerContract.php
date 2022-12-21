<?php

namespace WPFortress\Runtime\Contracts;

interface InvocationHandlerContract
{
    public function shouldHandle(InvocationContract $invocation): bool;

    public function handle(InvocationContract $invocation): InvocationResponseContract;
}
