<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;

final class CliEvent implements LambdaInvocationEventContract
{
    public static function shouldHandle(array $data): bool
    {
        return isset($data['cli']);
    }

    public static function fromResponseData(array $data): self
    {
        return new self(
            command: strval($data['cli'] ?? ''),
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
