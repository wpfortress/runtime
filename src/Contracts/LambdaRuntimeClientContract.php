<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use Throwable;

interface LambdaRuntimeClientContract
{
    public function retrieveNextInvocation(): LambdaInvocationContract;

    public function sendInvocationResponse(
        LambdaInvocationContract $invocation,
        InvocationResponseContract $response
    ): void;

    public function sendInvocationError(LambdaInvocationContract $invocation, Throwable $exception): void;

    public function sendInitialisationError(Throwable $exception): void;
}
