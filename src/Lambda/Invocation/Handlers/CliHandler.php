<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEvent;
use WPFortress\Runtime\Lambda\Invocation\Responses\CliResponse;

final class CliHandler implements LambdaInvocationHandlerContract
{
    public function shouldHandle(LambdaInvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof CliEvent;
    }

    public function handle(LambdaInvocationContract $invocation): LambdaInvocationResponseContract
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

        return CliResponse::fromProcess($process);
    }
}
