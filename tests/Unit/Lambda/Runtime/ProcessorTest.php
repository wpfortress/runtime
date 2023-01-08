<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Runtime;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerBusContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeClientContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeProcessorContract;
use WPFortress\Runtime\Lambda\Runtime\Processor;

final class ProcessorTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_runtime_processor_contract(): void
    {
        $stubbedProcessManager = $this->createStub(FastCGIProcessManagerContract::class);
        $stubbedRuntimeClient = $this->createStub(LambdaRuntimeClientContract::class);
        $stubbedInvocationHandlerBus = $this->createStub(LambdaInvocationHandlerBusContract::class);

        $processor = new Processor(
            processManager: $stubbedProcessManager,
            runtimeClient: $stubbedRuntimeClient,
            handlerBus: $stubbedInvocationHandlerBus,
        );

        self::assertInstanceOf(LambdaRuntimeProcessorContract::class, $processor);
    }
}
