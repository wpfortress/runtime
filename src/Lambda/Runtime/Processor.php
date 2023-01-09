<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Runtime;

use Throwable;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerBusContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeClientContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeProcessorContract;

final class Processor implements LambdaRuntimeProcessorContract
{
    public function __construct(
        private FastCGIProcessManagerContract $processManager,
        private LambdaRuntimeClientContract $runtimeClient,
        private LambdaInvocationHandlerBusContract $handlerBus,
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
            $response = $this->handlerBus->handle(invocation: $invocation);

            $this->runtimeClient->sendInvocationResponse(
                invocation: $invocation,
                response: $response,
            );
        } catch (Throwable $exception) {
            $this->runtimeClient->sendInvocationError(
                invocation: $invocation,
                exception: $exception,
            );
        }
    }
}
