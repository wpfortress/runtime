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
            isset($data['cli']) => CliEvent::fromResponseData($data),
            isset($data['requestContext']) => new HttpEvent($data),
            isset($data['ping']) => PingEvent::fromResponseData($data),
            isset($data['warm']) => WarmEvent::fromResponseData($data),
            default => throw new InvalidArgumentException('Unknown Lambda event type.'),
        };
    }
}
