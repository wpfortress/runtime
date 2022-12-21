<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEvent;
use WPFortress\Runtime\Lambda\Invocation\Responses\CliResponse;

final class CliHandler implements InvocationHandlerContract
{
    public function shouldHandle(InvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof CliEvent;
    }

    public function handle(InvocationContract $invocation): InvocationResponseContract
    {
        assert($invocation->getEvent() instanceof CliEvent);

        $command = "{$invocation->getEvent()->getCommand()} 2>&1";
        $timeout = max(1, $invocation->getContext()->getRemainingTimeInMs() / 1000 - 1);

        $process = Process::fromShellCommandline(
            command: $command,
            env: ['LAMBDA_INVOCATION_CONTEXT' => json_encode($invocation->getContext())],
            timeout: $timeout,
        );

        $process->run();

        return new CliResponse($process);
    }
}
