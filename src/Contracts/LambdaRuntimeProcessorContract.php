<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface LambdaRuntimeProcessorContract
{
    public function startFastCGIProcess(): void;

    public function processNextInvocation(): void;
}
