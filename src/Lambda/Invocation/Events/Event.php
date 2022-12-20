<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

abstract class Event
{
    /** @param array<string, mixed> $data */
    public function __construct(
        protected array $data,
    ) {
    }

    /** @return array<string, mixed> */
    public function getData(): array
    {
        return $this->data;
    }
}
