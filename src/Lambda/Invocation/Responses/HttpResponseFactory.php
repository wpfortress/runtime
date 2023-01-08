<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use InvalidArgumentException;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Contracts\InvocationStaticFileResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpErrorResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionOneEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionTwoEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\ApplicationLoadBalancerEvent;

final class HttpResponseFactory implements LambdaInvocationHttpResponseFactoryContract
{
    public function makeFromHttpErrorResponse(
        LambdaInvocationContract $invocation,
        LambdaInvocationHttpErrorResponseContract $response
    ): InvocationResponseContract {
        return match (get_class($invocation->getEvent())) {
            APIGatewayVersionOneEvent::class => APIGatewayVersionOneResponse::fromHttpErrorResponse($response),
            APIGatewayVersionTwoEvent::class => APIGatewayVersionTwoResponse::fromHttpErrorResponse($response),
            ApplicationLoadBalancerEvent::class => ApplicationLoadBalancerResponse::fromHttpErrorResponse($response),
            default => throw new InvalidArgumentException('Unhandled Lambda event type.'),
        };
    }

    public function makeFromFastCGIResponse(
        LambdaInvocationContract $invocation,
        ProvidesResponseData $response,
    ): InvocationResponseContract {
        return match (get_class($invocation->getEvent())) {
            APIGatewayVersionOneEvent::class => APIGatewayVersionOneResponse::fromFastCGIResponse($response),
            APIGatewayVersionTwoEvent::class => APIGatewayVersionTwoResponse::fromFastCGIResponse($response),
            ApplicationLoadBalancerEvent::class => ApplicationLoadBalancerResponse::fromFastCGIResponse($response),
            default => throw new InvalidArgumentException('Unhandled Lambda event type.'),
        };
    }

    public function makeFromStaticResponse(
        LambdaInvocationContract $invocation,
        InvocationStaticFileResponseContract $response,
    ): InvocationResponseContract {
        return match (get_class($invocation->getEvent())) {
            APIGatewayVersionOneEvent::class => APIGatewayVersionOneResponse::fromStaticResponse($response),
            APIGatewayVersionTwoEvent::class => APIGatewayVersionTwoResponse::fromStaticResponse($response),
            ApplicationLoadBalancerEvent::class => ApplicationLoadBalancerResponse::fromStaticResponse($response),
            default => throw new InvalidArgumentException('Unhandled Lambda event type.'),
        };
    }
}
