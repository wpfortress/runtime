<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class WarmEvent implements InvocationEventContract
{
    /** @param array{warm: int} $data */
    public static function fromResponseData(array $data): self
    {
        return new self(
            concurrency: $data['warm'],
        );
    }

    public function __construct(
        private int $concurrency,
    ) {
    }

    public function getConcurrency(): int
    {
        return $this->concurrency;
    }
}
