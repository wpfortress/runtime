<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;
use WPFortress\Runtime\Contracts\InvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\NotFoundHttpResponse;
use WPFortress\Runtime\Lambda\Invocation\Responses\StaticFileResponse;

abstract class AbstractHttpHandler
{
    public function __construct(
        protected InvocationHttpResponseFactoryContract $httpResponseFactory,
        protected string $rootDirectory,
    ) {
    }

    public function shouldHandle(InvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof InvocationHttpEventContract;
    }

    public function handle(InvocationContract $invocation): InvocationResponseContract
    {
        assert($invocation->getEvent() instanceof InvocationHttpEventContract);

        $filename = $this->resolveRequestedFilenameFrom($invocation);

        if (!$this->isPubliclyAccessible($filename)) {
            return $this->httpResponseFactory->makeFromHttpErrorResponse(
                invocation: $invocation,
                response: NotFoundHttpResponse::make(),
            );
        }
        var_dump($filename,!is_dir($filename), file_exists($filename));
        if ($this->isStaticFile($filename)) {
            var_dump($filename);
            return $this->httpResponseFactory->makeFromStaticResponse(
                invocation: $invocation,
                response: StaticFileResponse::fromFilename($filename),
            );
        }

        return $this->createInvocationResponse($invocation);
    }

    protected function resolveRequestedFilenameFrom(InvocationContract $invocation): string
    {
        return $this->rootDirectory . '/' . ltrim($invocation->getEvent()->getPath(), '/');
    }

    protected function isPubliclyAccessible(string $filename): bool
    {
        return true;
    }

    protected function isStaticFile(string $filename): bool
    {
        return !is_dir($filename) && file_exists($filename);
    }

    abstract protected function createInvocationResponse(InvocationContract $invocation): InvocationResponseContract;
}
