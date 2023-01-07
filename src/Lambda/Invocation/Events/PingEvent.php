<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class PingEvent implements InvocationEventContract
{
    public static function shouldHandle(array $data): bool
    {
        return isset($data['ping']);
    }

    public static function fromResponseData(array $data): self
    {
        return new self();
    }
}
