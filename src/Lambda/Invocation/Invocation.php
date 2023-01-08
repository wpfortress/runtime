<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation;

use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;

final class Invocation implements LambdaInvocationContract
{
    public function __construct(
        private LambdaInvocationContextContract $context,
        private LambdaInvocationEventContract $event,
    ) {
    }

    public function getContext(): LambdaInvocationContextContract
    {
        return $this->context;
    }

    public function getEvent(): LambdaInvocationEventContract|LambdaInvocationHttpEventContract
    {
        return $this->event;
    }
}
