<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation;

use WPFortress\Runtime\Contracts\InvocationContextContract;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationEventContract;

final class Invocation implements InvocationContract
{
    public function __construct(
        private InvocationContextContract $context,
        private InvocationEventContract $event,
    ) {
    }

    public function getContext(): InvocationContextContract
    {
        return $this->context;
    }

    public function getEvent(): InvocationEventContract
    {
        return $this->event;
    }
}
