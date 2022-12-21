<?php

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class WarmResponse implements InvocationResponseContract
{
    public function toApiGatewayFormat(): array
    {
        return ['Lambda is warm'];
    }
}
