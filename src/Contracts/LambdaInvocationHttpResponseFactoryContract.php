<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;

interface LambdaInvocationHttpResponseFactoryContract
{
    public function makeFromHttpErrorResponse(
        LambdaInvocationContract $invocation,
        LambdaInvocationHttpErrorResponseContract $response
    ): InvocationResponseContract;

    public function makeFromFastCGIResponse(
        LambdaInvocationContract $invocation,
        ProvidesResponseData $response
    ): InvocationResponseContract;

    public function makeFromStaticResponse(
        LambdaInvocationContract $invocation,
        LambdaInvocationStaticFileResponseContract $response,
    ): InvocationResponseContract;
}
