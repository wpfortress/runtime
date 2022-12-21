<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use AsyncAws\Core\Result;
use AsyncAws\Lambda\Input\InvocationRequest;
use AsyncAws\Lambda\LambdaClient;
use RuntimeException;
use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\WarmEvent;
use WPFortress\Runtime\Lambda\Invocation\Responses\WarmResponse;

final class WarmHandler implements InvocationHandlerContract
{
    public function __construct(
        private LambdaClient $lambdaClient,
    ) {
    }

    public function shouldHandle(InvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof WarmEvent;
    }

    public function handle(InvocationContract $invocation): InvocationResponseContract
    {
        assert($invocation->getEvent() instanceof WarmEvent);

        $concurrency = $invocation->getEvent()->getConcurrency();
        if ($concurrency <= 1) {
            return new WarmResponse();
        }

        $functionName = getenv('AWS_LAMBDA_FUNCTION_NAME');
        if (!is_string($functionName)) {
            throw new RuntimeException('"AWS_LAMBDA_FUNCTION_NAME" environment variable is missing.');
        }

        $promises = [];
        for ($i = 0; $i < $concurrency; ++$i) {
            $promises[] = $this->lambdaClient->invoke(
                new InvocationRequest([
                    'FunctionName' => $functionName,
                    'Qualifier' => 'deployed',
                    'InvocationType' => 'Event',
                    'LogType' => 'None',
                    'Payload' => '{"ping":true}',
                ])
            );
        }

        Result::wait($promises);

        return new WarmResponse();
    }
}
