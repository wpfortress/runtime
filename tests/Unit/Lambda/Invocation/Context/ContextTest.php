<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Lambda\Invocation\Context;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\InvocationContextContract;
use WPFortress\Runtime\Lambda\Invocation\Context\Context;

final class ContextTest extends TestCase
{
    /** @test */
    public function it_forms_correct_context(): void
    {
        $expectedAwsRequestId = '8476a536-e9f4-11e8-9739-2dfe598c3fcd';
        $expectedDeadlineInMs = intval(microtime(true) * 1000) + 100;
        $expectedRemainingTimeInMs = 100;
        $expectedInvokedFunctionArn = 'arn:aws:lambda:us-east-2:123456789012:function:custom-runtime';
        $expectedTraceId = 'Root=1-5bef4de7-ad49b0e87f6ef6c87fc2e700;Parent=9a9197af755a6419;Sampled=1';

        $context = new Context(
            awsRequestId: $expectedAwsRequestId,
            deadlineInMs: $expectedDeadlineInMs,
            remainingTimeInMs: $expectedRemainingTimeInMs,
            invokedFunctionArn: $expectedInvokedFunctionArn,
            traceId: $expectedTraceId,
        );

        self::assertInstanceOf(InvocationContextContract::class, $context);
        self::assertSame($expectedAwsRequestId, $context->getAwsRequestId());
        self::assertSame($expectedDeadlineInMs, $context->getDeadlineInMs());
        self::assertSame($expectedRemainingTimeInMs, $context->getRemainingTimeInMs());
        self::assertSame($expectedInvokedFunctionArn, $context->getInvokedFunctionArn());
        self::assertSame($expectedTraceId, $context->getTraceId());
    }
}
