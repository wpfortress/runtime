<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use WPFortress\Runtime\Contracts\InvocationFactoryContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeClientContract;

final class RuntimeClient implements LambdaRuntimeClientContract
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private InvocationFactoryContract $invocationFactory,
    ) {
    }

    public function retrieveNextInvocation(): LambdaInvocationContract
    {
        return $this->invocationFactory->make(
            response: $this->httpClient->request(
                method: 'GET',
                url: '/2018-06-01/runtime/invocation/next',
            )
        );
    }

    public function sendInvocationResponse(
        LambdaInvocationContract $invocation,
        InvocationResponseContract $response
    ): void {
        $this->httpClient->request(
            method: 'POST',
            url: "/2018-06-01/runtime/invocation/{$invocation->getContext()->getAwsRequestId()}/response",
            options: ['json' => $response],
        );
    }

    public function sendInvocationError(LambdaInvocationContract $invocation, Throwable $exception): void
    {
        $this->httpClient->request(
            method: 'POST',
            url: "/2018-06-01/runtime/invocation/{$invocation->getContext()->getAwsRequestId()}/error",
            options: [
                'headers' => $this->resolveErrorHeaders($exception),
                'json' => $this->resolveErrorPayload($exception),
            ],
        );
    }

    public function sendInitialisationError(Throwable $exception): void
    {
        $this->httpClient->request(
            method: 'POST',
            url: '/2018-06-01/runtime/init/error',
            options: [
                'headers' => $this->resolveErrorHeaders($exception),
                'json' => $this->resolveErrorPayload($exception),
            ],
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
