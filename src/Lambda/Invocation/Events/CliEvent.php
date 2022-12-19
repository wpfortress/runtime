<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEvent;

final class CliEvent extends Event implements InvocationEvent
{
    public function getCommand(): string
    {
        return $this->data['cli'] ?? '';
    }
}
