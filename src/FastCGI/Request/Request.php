<?php

declare(strict_types=1);

namespace WPFortress\Runtime\FastCGI\Request;

use hollodotme\FastCGI\Requests\AbstractRequest;

final class Request extends AbstractRequest
{
    private string $requestMethod = '';

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function setRequestMethod(string $requestMethod): void
    {
        $this->requestMethod = $requestMethod;
    }
}
