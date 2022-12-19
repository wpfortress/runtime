<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEvent;

final class WarmEvent extends Event implements InvocationEvent
{
    public function getConcurrency(): int
    {
        return $this->data['warm'] ?? 0;
    }
}
