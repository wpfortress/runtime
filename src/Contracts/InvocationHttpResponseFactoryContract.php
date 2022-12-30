<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;

interface InvocationHttpResponseFactoryContract
{
    public function makeFromHttpErrorResponse(
        InvocationContract $invocation,
        InvocationHttpErrorResponseContract $response
    ): InvocationResponseContract;

    public function makeFromFastCGIResponse(
        InvocationContract $invocation,
        ProvidesResponseData $response
    ): InvocationResponseContract;
}
