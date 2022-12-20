<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation;

use Symfony\Contracts\HttpClient\ResponseInterface;
use WPFortress\Runtime\Contracts\InvocationContextFactoryContract;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationEventFactoryContract;
use WPFortress\Runtime\Contracts\InvocationFactoryContract;

final class InvocationFactory implements InvocationFactoryContract
{
    public function __construct(
        private InvocationContextFactoryContract $contextFactory,
        private InvocationEventFactoryContract $eventFactory,
    ) {
    }

    public function make(ResponseInterface $response): InvocationContract
    {
        return new Invocation(
            context: $this->contextFactory->make($response->getHeaders()),
            event: $this->eventFactory->make($response->toArray()),
        );
    }
}
