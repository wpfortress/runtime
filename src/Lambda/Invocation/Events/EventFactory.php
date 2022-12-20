<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use InvalidArgumentException;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Contracts\InvocationEventFactoryContract;

final class EventFactory implements InvocationEventFactoryContract
{
    public function make(array $data): InvocationEventContract
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
