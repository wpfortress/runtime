<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Context;

use WPFortress\Runtime\Contracts\InvocationContext;
use WPFortress\Runtime\Contracts\InvocationContextFactory;

final class ContextFactory implements InvocationContextFactory
{
    public function make(array $headers): InvocationContext
    {
        return new Context(
            awsRequestId: $headers['lambda-runtime-aws-request-id'][0] ?? '',
            deadlineInMs: $deadlineInMs = intval($headers['lambda-runtime-deadline-ms'][0] ?? 0),
            remainingTimeInMs: $deadlineInMs - intval(microtime(true) * 1000),
            invokedFunctionArn: $headers['lambda-runtime-invoked-function-arn'][0] ?? '',
            traceId: $headers['lambda-runtime-trace-id'][0] ?? '',
        );
    }
}
