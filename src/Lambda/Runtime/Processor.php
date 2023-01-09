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
        private FastCGIProcessManagerContract $fastCGIProcessManager,
        private LambdaRuntimeClientContract $lambdaRuntimeClient,
        private LambdaInvocationHandlerBusContract $lambdaInvocationHandlerBus,
    ) {
    }

    public function startFastCGIProcess(): void
    {
        try {
            $this->fastCGIProcessManager->start();
        } catch (Throwable $exception) {
            $this->lambdaRuntimeClient->sendInitialisationError(exception: $exception);
        }
    }

    public function processNextInvocation(): void
    {
        $invocation = $this->lambdaRuntimeClient->retrieveNextInvocation();

        try {
            $response = $this->lambdaInvocationHandlerBus->handle(invocation: $invocation);

            $this->lambdaRuntimeClient->sendInvocationResponse(
                invocation: $invocation,
                response: $response,
            );
        } catch (Throwable $exception) {
            $this->lambdaRuntimeClient->sendInvocationError(
                invocation: $invocation,
                exception: $exception,
            );
        }
    }
}
