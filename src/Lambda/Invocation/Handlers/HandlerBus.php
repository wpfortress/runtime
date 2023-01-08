<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use InvalidArgumentException;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerBusContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;

final class HandlerBus implements InvocationHandlerBusContract
{
    /** @param iterable<InvocationHandlerContract> $handlers */
    public function __construct(
        private iterable $handlers,
    ) {
    }

    public function handle(LambdaInvocationContract $invocation): InvocationHandlerContract
    {
        foreach ($this->handlers as $handler) {
            if ($handler->shouldHandle($invocation)) {
                return $handler;
            }
        }

        throw new InvalidArgumentException('Unhandled Lambda invocation.');
    }
}
