<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;

final class PingResponse implements LambdaInvocationResponseContract
{
    /** @return list<string> */
    public function jsonSerialize(): array
    {
        return ['Pong'];
    }
}
