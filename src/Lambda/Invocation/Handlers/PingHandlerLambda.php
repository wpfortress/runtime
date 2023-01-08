<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEventLambda;
use WPFortress\Runtime\Lambda\Invocation\Responses\PingResponse;

final class PingHandlerLambda implements LambdaInvocationHandlerContract
{
    public function shouldHandle(LambdaInvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof PingEventLambda;
    }

    public function handle(LambdaInvocationContract $invocation): InvocationResponseContract
    {
        assert($invocation->getEvent() instanceof PingEventLambda);

        usleep(10000); // 10 ms

        return new PingResponse();
    }
}
