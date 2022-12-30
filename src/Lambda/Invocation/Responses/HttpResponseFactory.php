<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use InvalidArgumentException;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionOneEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionTwoEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\ApplicationLoadBalancerEvent;

final class HttpResponseFactory implements InvocationHttpResponseFactoryContract
{
    public function makeFromFastCGIResponse(
        InvocationContract $invocation,
        ProvidesResponseData $response,
    ): InvocationResponseContract {
        return match (get_class($invocation->getEvent())) {
            APIGatewayVersionOneEvent::class => APIGatewayVersionOneResponse::fromFastCGIResponse($response),
            APIGatewayVersionTwoEvent::class => APIGatewayVersionTwoResponse::fromFastCGIResponse($response),
            ApplicationLoadBalancerEvent::class => ApplicationLoadBalancerResponse::fromFastCGIResponse($response),
            default => throw new InvalidArgumentException('Unhandled Lambda event type.'),
        };
    }
}
