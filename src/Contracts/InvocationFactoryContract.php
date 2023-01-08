<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface InvocationFactoryContract
{
    public function make(ResponseInterface $response): LambdaInvocationContract;
}
