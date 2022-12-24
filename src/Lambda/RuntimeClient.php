<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationFactoryContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class RuntimeClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private InvocationFactoryContract $invocationFactory,
    ) {
    }

    public function retrieveNextInvocation(): InvocationContract
    {
        return $this->invocationFactory->make(
            $this->httpClient->request('GET', '/2018-06-01/runtime/invocation/next')
        );
    }

    public function sendInvocationResponse(InvocationContract $invocation, InvocationResponseContract $response): void
    {
        $this->httpClient->request(
            'POST',
            "/2018-06-01/runtime/invocation/{$invocation->getContext()->getAwsRequestId()}/response",
            ['json' => $response]
        );
    }

    public function sendInvocationError(InvocationContract $invocation, Throwable $exception): void
    {
        $this->httpClient->request(
            'POST',
            "/2018-06-01/runtime/invocation/{$invocation->getContext()->getAwsRequestId()}/error",
            ['headers' => $this->resolveErrorHeaders($exception), 'json' => $this->resolveErrorPayload($exception)]
        );
    }

    public function sendInitialisationError(Throwable $exception): void
    {
        $this->httpClient->request(
            'POST',
            '/2018-06-01/runtime/init/error',
            ['headers' => $this->resolveErrorHeaders($exception), 'json' => $this->resolveErrorPayload($exception)]
        );
    }

    /** @return array<string, string> */
    private function resolveErrorHeaders(Throwable $exception): array
    {
        return [
            'Lambda-Runtime-Function-Error-Type' => 'Unhandled',
        ];
    }

    /** @return array<string, mixed> */
    private function resolveErrorPayload(Throwable $error): array
    {
        return [
            'errorType' => get_class($error),
            'errorMessage' => $error->getMessage(),
            'stackTrace' => explode(PHP_EOL, $error->getTraceAsString()),
        ];
    }
}
