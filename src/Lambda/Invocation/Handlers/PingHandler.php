<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\PingEvent;
use WPFortress\Runtime\Lambda\Invocation\Responses\PingResponse;

final class PingHandler implements InvocationHandlerContract
{
    public function shouldHandle(InvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof PingEvent;
    }

    public function handle(InvocationContract $invocation): InvocationResponseContract
    {
        assert($invocation->getEvent() instanceof PingEvent);

        usleep(10000); // 10 ms

        return new PingResponse();
    }
}
