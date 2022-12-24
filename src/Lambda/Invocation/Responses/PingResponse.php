<?php

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use JsonSerializable;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class PingResponse implements InvocationResponseContract, JsonSerializable
{
    /** @return list<string> */
    public function jsonSerialize(): array
    {
        return ['Pong'];
    }
}
