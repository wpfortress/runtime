<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use InvalidArgumentException;
use WPFortress\Runtime\Contracts\InvocationEvent;
use WPFortress\Runtime\Contracts\InvocationEventFactory;

final class EventFactory implements InvocationEventFactory
{
    public function make(array $data): InvocationEvent
    {
        return match (true) {
            isset($data['cli']) => new CliEvent($data),
            isset($data['requestContext']) => new HttpEvent($data),
            isset($data['ping']) => new PingEvent($data),
            isset($data['warm']) => new WarmEvent($data),
            default => throw new InvalidArgumentException('Unknown Lambda event type.'),
        };
    }
}
