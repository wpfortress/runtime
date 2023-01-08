<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Context;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationContextFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\Lambda\Invocation\Context\ContextFactory;

final class ContextFactoryTest extends TestCase
{
    /** @test */
    public function it_implements_invocation_context_factory_contract(): void
    {
        $contextFactory = new ContextFactory();

        self::assertInstanceOf(InvocationContextFactoryContract::class, $contextFactory);
    }

    /** @test */
    public function it_makes_context_from_response_headers(): void
    {
        $expectedAwsRequestId = '8476a536-e9f4-11e8-9739-2dfe598c3fcd';
        $expectedDeadlineInMs = intval(microtime(true) * 1000) + 100;
        $expectedInvokedFunctionArn = 'arn:aws:lambda:us-east-2:123456789012:function:custom-runtime';
        $expectedTraceId = 'Root=1-5bef4de7-ad49b0e87f6ef6c87fc2e700;Parent=9a9197af755a6419;Sampled=1';

        $headers = [
            'lambda-runtime-aws-request-id' => [$expectedAwsRequestId],
            'lambda-runtime-deadline-ms' => [(string)$expectedDeadlineInMs],
            'lambda-runtime-invoked-function-arn' => [$expectedInvokedFunctionArn],
            'lambda-runtime-trace-id' => [$expectedTraceId],
        ];

        $contextFactory = new ContextFactory();
        $context = $contextFactory->make($headers);

        self::assertInstanceOf(LambdaInvocationContextContract::class, $context);
        self::assertSame($expectedAwsRequestId, $context->getAwsRequestId());
        self::assertSame($expectedDeadlineInMs, $context->getDeadlineInMs());
        self::assertTrue($context->getRemainingTimeInMs() > 0);
        self::assertSame($expectedInvokedFunctionArn, $context->getInvokedFunctionArn());
        self::assertSame($expectedTraceId, $context->getTraceId());
    }

    /** @test */
    public function it_makes_context_from_empty_response_headers(): void
    {
        $headers = [];

        $contextFactory = new ContextFactory();
        $context = $contextFactory->make($headers);

        self::assertInstanceOf(LambdaInvocationContextContract::class, $context);
        self::assertSame('', $context->getAwsRequestId());
        self::assertSame(0, $context->getDeadlineInMs());
        self::assertTrue($context->getRemainingTimeInMs() < 0);
        self::assertSame('', $context->getInvokedFunctionArn());
        self::assertSame('', $context->getTraceId());
    }
}
