<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use InvalidArgumentException;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerCollectionContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;

final class HandlerCollection implements InvocationHandlerCollectionContract
{
    /** @param InvocationHandlerContract[] $handlers */
    public function __construct(
        private iterable $handlers,
    ) {
    }

    public function pickFor(InvocationContract $invocation): InvocationHandlerContract
    {
        foreach ($this->handlers as $handler) {
            if ($handler->shouldHandle($invocation)) {
                return $handler;
            }
        }

        throw new InvalidArgumentException('Unhandled Lambda invocation.');
    }
}
