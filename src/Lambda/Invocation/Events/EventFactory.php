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
            isset($data['requestContext']['elb']) => ApplicationLoadBalancerEvent::fromResponseData($data),
            isset($data['httpMethod']) => APIGatewayVersionOneEvent::fromResponseData($data),
            isset($data['requestContext']['http']['method']) => APIGatewayVersionTwoEvent::fromResponseData($data),
            isset($data['cli']) => CliEvent::fromResponseData($data),
            isset($data['ping']) => PingEvent::fromResponseData($data),
            isset($data['warm']) => WarmEvent::fromResponseData($data),
            default => throw new InvalidArgumentException('Unknown Lambda event type.'),
        };
    }
}
