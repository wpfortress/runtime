<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use AsyncAws\Core\Result;
use AsyncAws\Lambda\Input\InvocationRequest;
use AsyncAws\Lambda\LambdaClient;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\WarmEvent;
use WPFortress\Runtime\Lambda\Invocation\Responses\WarmResponse;

final class WarmHandler implements LambdaInvocationHandlerContract
{
    public function __construct(
        private LambdaClient $lambdaClient,
        private string $lambdaFunctionName,
    ) {
    }

    public function shouldHandle(LambdaInvocationContract $invocation): bool
    {
        return $invocation->getEvent() instanceof WarmEvent;
    }

    public function handle(LambdaInvocationContract $invocation): LambdaInvocationResponseContract
    {
        assert($invocation->getEvent() instanceof WarmEvent);

        $concurrency = $invocation->getEvent()->getConcurrency();
        if ($concurrency <= 1) {
            return new WarmResponse();
        }

        $promises = [];
        for ($i = 0; $i < $concurrency; ++$i) {
            $promises[] = $this->lambdaClient->invoke(
                new InvocationRequest([
                    'FunctionName' => $this->lambdaFunctionName,
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
