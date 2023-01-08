<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation;

use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;

final class Invocation implements LambdaInvocationContract
{
    public function __construct(
        private LambdaInvocationContextContract $context,
        private InvocationEventContract $event,
    ) {
    }

    public function getContext(): LambdaInvocationContextContract
    {
        return $this->context;
    }

    public function getEvent(): InvocationEventContract|InvocationHttpEventContract
    {
        return $this->event;
    }
}
