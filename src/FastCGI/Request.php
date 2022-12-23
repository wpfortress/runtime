<?php

declare(strict_types=1);

namespace WPFortress\Runtime\FastCGI;

use hollodotme\FastCGI\Requests\AbstractRequest;

final class Request extends AbstractRequest
{
    public function __construct(
        private string $method,
        string $scriptFilename,
        string $content
    ) {
        parent::__construct($scriptFilename, $content);
    }

    public function getRequestMethod(): string
    {
        return $this->method;
    }

    public function getServerSoftware(): string
    {
        return 'WPFortress';
    }
}
