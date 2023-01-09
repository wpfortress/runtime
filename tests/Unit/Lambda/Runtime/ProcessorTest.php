<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Runtime;

use Exception;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerBusContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeClientContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeProcessorContract;
use WPFortress\Runtime\Lambda\Runtime\Processor;

final class ProcessorTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_runtime_processor_contract(): void
    {
        $stubbedFastCGIProcessManager = $this->createStub(FastCGIProcessManagerContract::class);
        $stubbedLambdaRuntimeClient = $this->createStub(LambdaRuntimeClientContract::class);
        $stubbedLambdaInvocationHandlerBus = $this->createStub(LambdaInvocationHandlerBusContract::class);

        $processor = new Processor(
            processManager: $stubbedFastCGIProcessManager,
            runtimeClient: $stubbedLambdaRuntimeClient,
            handlerBus: $stubbedLambdaInvocationHandlerBus,
        );

        self::assertInstanceOf(LambdaRuntimeProcessorContract::class, $processor);
    }

    /** @test */
    public function it_sends_initialisation_error_whenever_fastcgi_process_fails_to_start(): void
    {
        $mockedFastCGIProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $mockedLambdaRuntimeClient = $this->createMock(LambdaRuntimeClientContract::class);
        $stubbedLambdaInvocationHandlerBus = $this->createStub(LambdaInvocationHandlerBusContract::class);

        $exception = new Exception();

        $mockedFastCGIProcessManager
            ->expects(self::once())
            ->method('start')
            ->willThrowException($exception);

        $mockedLambdaRuntimeClient
            ->expects(self::once())
            ->method('sendInitialisationError')
            ->with(self::equalTo($exception));

        $processor = new Processor(
            processManager: $mockedFastCGIProcessManager,
            runtimeClient: $mockedLambdaRuntimeClient,
            handlerBus: $stubbedLambdaInvocationHandlerBus,
        );
        $processor->startFastCGIProcess();
    }

    /** @test */
    public function it_starts_fastcgi_process(): void
    {
        $mockedFastCGIProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $stubbedLambdaRuntimeClient = $this->createStub(LambdaRuntimeClientContract::class);
        $stubbedLambdaInvocationHandlerBus = $this->createStub(LambdaInvocationHandlerBusContract::class);

        $mockedFastCGIProcessManager
            ->expects(self::once())
            ->method('start');

        $processor = new Processor(
            processManager: $mockedFastCGIProcessManager,
            runtimeClient: $stubbedLambdaRuntimeClient,
            handlerBus: $stubbedLambdaInvocationHandlerBus,
        );
        $processor->startFastCGIProcess();
    }

    /** @test */
    public function it_sends_invocation_error_whenever_fails_to_process(): void
    {
        $stubbedFastCGIProcessManager = $this->createStub(FastCGIProcessManagerContract::class);
        $mockedLambdaRuntimeClient = $this->createMock(LambdaRuntimeClientContract::class);
        $mockedLambdaInvocationHandlerBus = $this->createMock(LambdaInvocationHandlerBusContract::class);
        $stubbedLambdaInvocation = $this->createStub(LambdaInvocationContract::class);

        $exception = new Exception();

        $mockedLambdaRuntimeClient
            ->expects(self::once())
            ->method('retrieveNextInvocation')
            ->willReturn($stubbedLambdaInvocation);

        $mockedLambdaRuntimeClient
            ->expects(self::once())
            ->method('sendInvocationError')
            ->with(self::equalTo($stubbedLambdaInvocation), self::equalTo($exception));

        $mockedLambdaInvocationHandlerBus
            ->expects(self::once())
            ->method('handle')
            ->with(self::equalTo($stubbedLambdaInvocation))
            ->willThrowException(new Exception());

        $processor = new Processor(
            processManager: $stubbedFastCGIProcessManager,
            runtimeClient: $mockedLambdaRuntimeClient,
            handlerBus: $mockedLambdaInvocationHandlerBus,
        );
        $processor->processNextInvocation();
    }

    /** @test */
    public function it_processes_next_invocation(): void
    {
        $stubbedFastCGIProcessManager = $this->createStub(FastCGIProcessManagerContract::class);
        $mockedLambdaRuntimeClient = $this->createMock(LambdaRuntimeClientContract::class);
        $mockedLambdaInvocationHandlerBus = $this->createMock(LambdaInvocationHandlerBusContract::class);
        $stubbedLambdaInvocation = $this->createStub(LambdaInvocationContract::class);
        $stubbedLambdaInvocationResponse = $this->createStub(LambdaInvocationResponseContract::class);

        $mockedLambdaRuntimeClient
            ->expects(self::once())
            ->method('retrieveNextInvocation')
            ->willReturn($stubbedLambdaInvocation);

        $mockedLambdaRuntimeClient
            ->expects(self::once())
            ->method('sendInvocationResponse')
            ->with(self::equalTo($stubbedLambdaInvocation), self::equalTo($stubbedLambdaInvocationResponse));

        $mockedLambdaInvocationHandlerBus
            ->expects(self::once())
            ->method('handle')
            ->with(self::equalTo($stubbedLambdaInvocation))
            ->willReturn($stubbedLambdaInvocationResponse);

        $processor = new Processor(
            processManager: $stubbedFastCGIProcessManager,
            runtimeClient: $mockedLambdaRuntimeClient,
            handlerBus: $mockedLambdaInvocationHandlerBus,
        );
        $processor->processNextInvocation();
    }
}
