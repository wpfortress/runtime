<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class CliEvent extends Event implements InvocationEventContract
{
    public function getCommand(): string
    {
        return $this->data['cli'] ?? '';
    }
}
