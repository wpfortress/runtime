<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Console\Commands;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WPFortress\Runtime\Console\Commands\ProcessCommand;
use WPFortress\Runtime\Contracts\LambdaRuntimeProcessorContract;

final class ProcessCommandTest extends TestCase
{
    /** @test */
    public function it_extends_base_command(): void
    {
        $stubbedLambdaRuntimeProcessor = $this->createMock(LambdaRuntimeProcessorContract::class);

        $command = new ProcessCommand($stubbedLambdaRuntimeProcessor);

        self::assertInstanceOf(Command::class, $command);
    }

    /** @test */
    public function it_configures_command(): void
    {
        $stubbedLambdaRuntimeProcessor = $this->createMock(LambdaRuntimeProcessorContract::class);

        $command = new ProcessCommand($stubbedLambdaRuntimeProcessor);

        self::assertSame('process', $command->getName());
        self::assertTrue($command->getDefinition()->hasOption('with-fastcgi'));
        self::assertTrue($command->getDefinition()->hasOption('max-invocations'));
    }

    /** @test */
    public function it_processes_lambda_invocations_without_fastcgi(): void
    {
        $mockedLambdaRuntimeProcessor = $this->createMock(LambdaRuntimeProcessorContract::class);
        $mockedInput = $this->createMock(InputInterface::class);
        $stubbedOutput = $this->createStub(OutputInterface::class);

        $maxInvocation = 5;

        $mockedLambdaRuntimeProcessor
            ->expects(self::never())
            ->method('startFastCGIProcess');

        $mockedLambdaRuntimeProcessor
            ->expects(self::exactly($maxInvocation))
            ->method('processNextInvocation');

        $mockedInput
            ->expects(self::exactly(2))
            ->method('getOption')
            ->withConsecutive(
                [self::equalTo('with-fastcgi')],
                [self::equalTo('max-invocations')],
            )
            ->willReturnOnConsecutiveCalls(
                false,
                $maxInvocation,
            );

        $command = new ProcessCommand($mockedLambdaRuntimeProcessor);
        $exitCode = $command->run($mockedInput, $stubbedOutput);

        self::assertSame(Command::SUCCESS, $exitCode);
    }

    /** @test */
    public function it_processes_lambda_invocations_with_fastcgi(): void
    {
        $mockedLambdaRuntimeProcessor = $this->createMock(LambdaRuntimeProcessorContract::class);
        $mockedInput = $this->createMock(InputInterface::class);
        $stubbedOutput = $this->createStub(OutputInterface::class);

        $maxInvocation = 5;

        $mockedLambdaRuntimeProcessor
            ->expects(self::once())
            ->method('startFastCGIProcess');

        $mockedLambdaRuntimeProcessor
            ->expects(self::exactly($maxInvocation))
            ->method('processNextInvocation');

        $mockedInput
            ->expects(self::exactly(2))
            ->method('getOption')
            ->withConsecutive(
                [self::equalTo('with-fastcgi')],
                [self::equalTo('max-invocations')],
            )
            ->willReturnOnConsecutiveCalls(
                true,
                $maxInvocation,
            );

        $command = new ProcessCommand($mockedLambdaRuntimeProcessor);
        $exitCode = $command->run($mockedInput, $stubbedOutput);

        self::assertSame(Command::SUCCESS, $exitCode);
    }
}
