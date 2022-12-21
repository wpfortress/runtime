<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationResponseContract
{
    /** @return array<string, mixed> */
    public function toApiGatewayFormat(float $formatVersion): array;
}
