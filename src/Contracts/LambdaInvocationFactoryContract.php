<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface LambdaInvocationFactoryContract
{
    public function make(ResponseInterface $response): LambdaInvocationContract;
}
