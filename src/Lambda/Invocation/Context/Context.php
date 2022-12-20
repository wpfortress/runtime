<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Context;

use WPFortress\Runtime\Contracts\InvocationContextContract;

final class Context implements InvocationContextContract
{
    public function __construct(
        private string $awsRequestId,
        private int $deadlineInMs,
        private int $remainingTimeInMs,
        private string $invokedFunctionArn,
        private string $traceId,
    ) {
    }

    public function getAwsRequestId(): string
    {
        return $this->awsRequestId;
    }

    public function getDeadlineInMs(): int
    {
        return $this->deadlineInMs;
    }

    public function getRemainingTimeInMs(): int
    {
        return $this->remainingTimeInMs;
    }

    public function getInvokedFunctionArn(): string
    {
        return $this->invokedFunctionArn;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }
}
