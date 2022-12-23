<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class PingEvent implements InvocationEventContract
{
    /** @param array{ping: bool} $data */
    public static function fromResponseData(array $data): self
    {
        return new self();
    }
}
