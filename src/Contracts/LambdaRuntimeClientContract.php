<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use Throwable;

interface LambdaRuntimeClientContract
{
    public function retrieveNextInvocation(): InvocationContract;

    public function sendInvocationResponse(InvocationContract $invocation, InvocationResponseContract $response): void;

    public function sendInvocationError(InvocationContract $invocation, Throwable $exception): void;

    public function sendInitialisationError(Throwable $exception): void;
}
