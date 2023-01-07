<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationEventContract
{
    /** @param array<string, scalar|mixed[][]> $data */
    public static function shouldHandle(array $data): bool;

    /** @param array<string, scalar|mixed[][]> $data */
    public static function fromResponseData(array $data): self;
}
