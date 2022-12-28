<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;

interface FastCGIProcessClientContract
{
    public function sendRequest(ProvidesRequestData $request, ?int $timeoutMs = null): ProvidesResponseData;
}
