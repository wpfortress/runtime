<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;

interface FastCGIRequestFactoryContract
{
    public function make(InvocationContract $invocation, string $scriptFilename): ProvidesRequestData;
}
