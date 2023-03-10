<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Responses\CliResponse;

final class CliResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_response(): void
    {
        $mockedProcess = $this->createMock(Process::class);

        $mockedProcess
            ->expects(self::once())
            ->method('getExitCode')
            ->willReturn(0);
        $mockedProcess
            ->expects(self::once())
            ->method('getOutput')
            ->willReturn('foo');

        $response = CliResponse::fromProcess($mockedProcess);
        $result = $response->jsonSerialize();

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(JsonSerializable::class, $response);
        self::assertSame(0, $result['exitCode']);
        self::assertSame('foo', $result['output']);
    }
}
