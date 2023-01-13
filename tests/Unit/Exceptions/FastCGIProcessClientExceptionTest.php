<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\FastCGI\Process;

use Exception;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Exceptions\FastCGIProcessClientException;

final class FastCGIProcessClientExceptionTest extends TestCase
{
    /** @test */
    public function it_forms_correct_exception_with_timed_out_reason(): void
    {
        $exception = FastCGIProcessClientException::timedOut(timeoutMs: $timeoutMs = 1000);

        self::assertInstanceOf(Exception::class, $exception);
        self::assertSame("FastCGI request timed out after {$timeoutMs} ms.", $exception->getMessage());
    }

    /** @test */
    public function it_forms_correct_exception_with_failed_communication_reason(): void
    {
        $exception = FastCGIProcessClientException::communicationFailed(previous: $previous = new Exception());

        self::assertInstanceOf(Exception::class, $exception);
        self::assertSame('Unable to read a response from FastCGI service.', $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }
}
