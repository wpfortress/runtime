<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use JsonSerializable;
use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class CliResponse implements InvocationResponseContract, JsonSerializable
{
    public static function fromProcess(Process $process): self
    {
        return new self(
            exitCode: $process->getExitCode(),
            output: $process->getOutput(),
        );
    }

    public function __construct(
        private ?int $exitCode,
        private string $output,
    ) {
    }

    /** @return array{exitCode: ?int, output: string} */
    public function jsonSerialize(): array
    {
        return [
            'exitCode' => $this->exitCode,
            'output' => $this->output,
        ];
    }
}
