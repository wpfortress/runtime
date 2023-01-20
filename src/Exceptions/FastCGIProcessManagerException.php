<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Exceptions;

use Exception;
use Symfony\Component\Process\Process;

final class FastCGIProcessManagerException extends Exception
{
    public static function cannotBeStopped(): self
    {
        return new self(message: 'FastCGI process cannot be stopped.');
    }

    public static function unknownStop(): self
    {
        return new self(message: 'PHP-FPM has stopped for an unknown reason.');
    }

    public static function startTimeout(): self
    {
        return new self(message: 'Timeout while waiting for PHP-FPM to start.');
    }

    public static function startFailure(Process $process): self
    {
        return new self(
            'PHP-FPM failed to start: ' . PHP_EOL .
            $process->getOutput() . PHP_EOL .
            $process->getErrorOutput()
        );
    }

    public static function stopTimeout(): self
    {
        return new self(message: 'Timeout while waiting for PHP-FPM to stop.');
    }
}
