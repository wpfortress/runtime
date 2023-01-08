<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda;

use Throwable;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Contracts\InvocationHandlerBusContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeClientContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeProcessorContract;

final class RuntimeProcessor implements LambdaRuntimeProcessorContract
{
    public function __construct(
        private FastCGIProcessManagerContract $processManager,
        private LambdaRuntimeClientContract $runtimeClient,
        private InvocationHandlerBusContract $handlerBus,
    ) {
    }

    public function startFastCGIProcess(): void
    {
        try {
            $this->processManager->start();
        } catch (Throwable $exception) {
            $this->runtimeClient->sendInitialisationError(exception: $exception);
        }
    }

    public function processNextInvocation(): void
    {
        $invocation = $this->runtimeClient->retrieveNextInvocation();

        try {
            $this->runtimeClient->sendInvocationResponse(
                invocation: $invocation,
                response: $this->handlerBus->handle(invocation: $invocation)->handle(invocation: $invocation),
            );
        } catch (Throwable $exception) {
            $this->runtimeClient->sendInvocationError(
                invocation: $invocation,
                exception: $exception,
            );
        }
    }
}
