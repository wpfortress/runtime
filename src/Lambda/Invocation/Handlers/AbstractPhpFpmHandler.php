<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\FastCGIProcessClientContract;
use WPFortress\Runtime\Contracts\FastCGIRequestFactoryContract;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;
use WPFortress\Runtime\Contracts\InvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

abstract class AbstractPhpFpmHandler extends AbstractHttpHandler
{
    public function __construct(
        protected FastCGIRequestFactoryContract $requestFactory,
        protected FastCGIProcessClientContract $processClient,
        InvocationHttpResponseFactoryContract $httpResponseFactory,
        string $rootDirectory,
    ) {
        parent::__construct($httpResponseFactory, $rootDirectory);
    }

    protected function isStaticFile(string $filename): bool
    {
        return parent::isStaticFile($filename) && !str_contains($filename, '.php');
    }

    protected function createInvocationResponse(InvocationContract $invocation): InvocationResponseContract
    {
        assert($invocation->getEvent() instanceof InvocationHttpEventContract);

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

    abstract protected function resolveScriptFilenameFrom(InvocationHttpEventContract $event): string;
}
