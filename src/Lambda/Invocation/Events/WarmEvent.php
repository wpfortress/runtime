<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;

final class WarmEvent implements LambdaInvocationEventContract
{
    public static function shouldHandle(array $data): bool
    {
        return isset($data['warm']);
    }

    public static function fromResponseData(array $data): self
    {
        return new self(
            concurrency: intval($data['warm'] ?? 0),
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
