<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class CliEvent implements InvocationEventContract
{
    /** @param array{cli: string} $data */
    public static function fromResponseData(array $data): self
    {
        return new self(
            command: $data['cli'],
        );
    }

    public function __construct(
        private string $command,
    ) {
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
