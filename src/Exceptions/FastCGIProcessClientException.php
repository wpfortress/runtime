<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Exceptions;

use Exception;
use Throwable;

final class FastCGIProcessClientException extends Exception
{
    public static function timedOut(?int $timeoutMs = null): self
    {
        return new self(message: "FastCGI request timed out after {$timeoutMs} ms.");
    }

    public static function communicationFailed(Throwable $previous): self
    {
        return new self(message: 'Unable to read a response from FastCGI service.', previous: $previous);
    }
}
