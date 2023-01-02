<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationHandlerCollectionContract
{
    public function pickFor(InvocationContract $invocation): InvocationHandlerContract;
}
