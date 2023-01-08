<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Lambda\Invocation\Events\CliEventLambda;
use WPFortress\Runtime\Lambda\Invocation\Responses\CliResponse;

final class CliHandlerLambda implements LambdaInvocationHandlerContract
{
    public function shouldHandle(LambdaInvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof CliEventLambda;
    }

    public function handle(LambdaInvocationContract $invocation): InvocationResponseContract
    {
        assert($invocation->getEvent() instanceof CliEventLambda);

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
