<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\NotFoundHttpResponse;
use WPFortress\Runtime\Lambda\Invocation\Responses\StaticFileResponse;

abstract class AbstractHttpHandler
{
    public function __construct(
        protected LambdaInvocationHttpResponseFactoryContract $httpResponseFactory,
        protected string $lambdaRootDirectory,
    ) {
    }

    public function shouldHandle(LambdaInvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof LambdaInvocationHttpEventContract;
    }

    public function handle(LambdaInvocationContract $invocation): LambdaInvocationResponseContract
    {
        assert($invocation->getEvent() instanceof LambdaInvocationHttpEventContract);

        $filename = $this->resolveRequestedFilenameFrom($invocation->getEvent());

        if (!$this->isPubliclyAccessible($filename)) {
            return $this->httpResponseFactory->makeFromHttpErrorResponse(
                invocation: $invocation,
                response: NotFoundHttpResponse::make(),
            );
        }

        if ($this->isStaticFile($filename)) {
            return $this->httpResponseFactory->makeFromStaticResponse(
                invocation: $invocation,
                response: StaticFileResponse::fromFilename($filename),
            );
        }

        return $this->createInvocationResponse($invocation);
    }

    protected function resolveRequestedFilenameFrom(LambdaInvocationHttpEventContract $event): string
    {
        return $this->lambdaRootDirectory . '/' . ltrim($event->getPath(), '/');
    }

    protected function isPubliclyAccessible(string $filename): bool
    {
        return true;
    }

    protected function isStaticFile(string $filename): bool
    {
        return !is_dir($filename) && file_exists($filename);
    }

    abstract protected function createInvocationResponse(
        LambdaInvocationContract $invocation
    ): LambdaInvocationResponseContract;
}
