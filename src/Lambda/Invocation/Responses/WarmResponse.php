<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use JsonSerializable;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class WarmResponse implements InvocationResponseContract, JsonSerializable
{
    /** @return list<string> */
    public function jsonSerialize(): array
    {
        return ['Lambda is warm'];
    }
}
