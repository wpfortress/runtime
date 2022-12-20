<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class WarmEvent extends Event implements InvocationEventContract
{
    public function getConcurrency(): int
    {
        return $this->data['warm'] ?? 0;
    }
}
