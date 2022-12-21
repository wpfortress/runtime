<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class CliResponse implements InvocationResponseContract
{
    public function __construct(
        private Process $process,
    ) {
    }

    public function toApiGatewayFormat(): array
    {
        return [
            'exitCode' => $this->process->getExitCode(),
            'output' => $this->process->getOutput(),
        ];
    }
}
