<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationContext
{
    public function getAwsRequestId(): string;

    public function getDeadlineInMs(): int;

    public function getRemainingTimeInMs(): int;

    public function getInvokedFunctionArn(): string;

    public function getTraceId(): string;
}
