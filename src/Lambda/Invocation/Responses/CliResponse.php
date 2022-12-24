<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use JsonSerializable;
use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class CliResponse implements InvocationResponseContract, JsonSerializable
{
    public function __construct(
        private Process $process,
    ) {
    }

    /** @return array{exitCode: ?int, output: string} */
    public function jsonSerialize(): array
    {
        return [
            'exitCode' => $this->process->getExitCode(),
            'output' => $this->process->getOutput(),
        ];
    }
}
