<?php

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class PingResponse implements InvocationResponseContract
{
    public function toApiGatewayFormat(): array
    {
        return ['Pong'];
    }
}
