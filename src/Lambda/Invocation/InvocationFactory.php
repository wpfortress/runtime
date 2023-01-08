<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation;

use Symfony\Contracts\HttpClient\ResponseInterface;
use WPFortress\Runtime\Contracts\LambdaInvocationContextFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationEventFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationFactoryContract;

final class InvocationFactory implements LambdaInvocationFactoryContract
{
    public function __construct(
        private LambdaInvocationContextFactoryContract $contextFactory,
        private LambdaInvocationEventFactoryContract $eventFactory,
    ) {
    }

    public function make(ResponseInterface $response): LambdaInvocationContract
    {
        return new Invocation(
            context: $this->contextFactory->make($response->getHeaders()),
            event: $this->eventFactory->make($response->toArray()),
        );
    }
}
