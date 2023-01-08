<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use InvalidArgumentException;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Contracts\InvocationEventFactoryContract;

final class EventFactory implements InvocationEventFactoryContract
{
    /** @param iterable<class-string<LambdaInvocationEventContract>> $events */
    public function __construct(
        private iterable $events,
    ) {
    }

    public function make(array $data): LambdaInvocationEventContract
    {
        foreach ($this->events as $event) {
            if ($event::shouldHandle($data)) {
                return $event::fromResponseData($data);
            }
        }

        throw new InvalidArgumentException('Unknown Lambda event type.');
    }
}
