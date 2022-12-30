<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;

interface InvocationHttpResponseFactoryContract
{
    public function makeFromFastCGIResponse(
        InvocationContract $invocation,
        ProvidesResponseData $response
    ): InvocationResponseContract;
}
