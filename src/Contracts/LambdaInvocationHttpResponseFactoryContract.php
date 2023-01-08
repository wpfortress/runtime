<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;

interface LambdaInvocationHttpResponseFactoryContract
{
    public function makeFromHttpErrorResponse(
        LambdaInvocationContract $invocation,
        LambdaInvocationHttpErrorResponseContract $response
    ): LambdaInvocationResponseContract;

    public function makeFromFastCGIResponse(
        LambdaInvocationContract $invocation,
        ProvidesResponseData $response
    ): LambdaInvocationResponseContract;

    public function makeFromStaticResponse(
        LambdaInvocationContract $invocation,
        LambdaInvocationStaticFileResponseContract $response,
    ): LambdaInvocationResponseContract;
}
