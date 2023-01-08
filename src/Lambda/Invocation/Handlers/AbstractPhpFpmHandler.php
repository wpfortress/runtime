<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\FastCGIProcessClientContract;
use WPFortress\Runtime\Contracts\FastCGIRequestFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpResponseFactoryContract;

abstract class AbstractPhpFpmHandler extends AbstractHttpHandler
{
    public function __construct(
        protected FastCGIRequestFactoryContract $requestFactory,
        protected FastCGIProcessClientContract $processClient,
        LambdaInvocationHttpResponseFactoryContract $httpResponseFactory,
        string $lambdaRootDirectory,
    ) {
        parent::__construct($httpResponseFactory, $lambdaRootDirectory);
    }

    protected function isStaticFile(string $filename): bool
    {
        return parent::isStaticFile($filename) && !str_contains($filename, '.php');
    }

    protected function createInvocationResponse(LambdaInvocationContract $invocation): LambdaInvocationResponseContract
    {
        assert($invocation->getEvent() instanceof LambdaInvocationHttpEventContract);

        $request = $this->requestFactory->make(
            invocation: $invocation,
            scriptFilename: $this->resolveScriptFilenameFrom($invocation->getEvent()),
        );

        $oneSecond = 1000;
        $timeoutMs = max($oneSecond, $invocation->getContext()->getRemainingTimeInMs() - $oneSecond);

        $response = $this->processClient->sendRequest(
            request: $request,
            timeoutMs: $timeoutMs,
        );

        return $this->httpResponseFactory->makeFromFastCGIResponse(
            invocation: $invocation,
            response: $response,
        );
    }

    abstract protected function resolveScriptFilenameFrom(LambdaInvocationHttpEventContract $event): string;
}
