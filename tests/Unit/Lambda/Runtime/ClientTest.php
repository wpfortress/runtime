<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Runtime;

use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WPFortress\Runtime\Contracts\LambdaInvocationContextContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaRuntimeClientContract;
use WPFortress\Runtime\Lambda\Runtime\Client;

final class ClientTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_runtime_client_contract(): void
    {
        $stubbedHttpClient = $this->createStub(HttpClientInterface::class);
        $stubbedLambdaInvocationFactory = $this->createStub(LambdaInvocationFactoryContract::class);

        $client = new Client($stubbedHttpClient, $stubbedLambdaInvocationFactory);

        self::assertInstanceOf(LambdaRuntimeClientContract::class, $client);
    }

    /** @test */
    public function it_retrieves_next_invocation(): void
    {
        $mockedResponse = new MockResponse('', ['http_code' => 200]);
        $mockedHttpClient = new MockHttpClient($mockedResponse);
        $mockedInvocationFactory = $this->createMock(LambdaInvocationFactoryContract::class);
        $stubbedLambdaInvocation = $this->createStub(LambdaInvocationContract::class);

        $mockedInvocationFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn($stubbedLambdaInvocation);

        $client = new Client($mockedHttpClient, $mockedInvocationFactory);
        $invocation = $client->retrieveNextInvocation();

        self::assertSame('GET', $mockedResponse->getRequestMethod());
        self::assertStringEndsWith('/2018-06-01/runtime/invocation/next', $mockedResponse->getRequestUrl());
        self::assertSame($stubbedLambdaInvocation, $invocation);
    }

    /** @test */
    public function it_sends_invocation_response(): void
    {
        $mockedResponse = new MockResponse('', ['http_code' => 202]);
        $mockedHttpClient = new MockHttpClient($mockedResponse);
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedLambdaInvocationResponse = $this->createMock(LambdaInvocationResponseContract::class);
        $stubbedLambdaInvocationFactory = $this->createStub(LambdaInvocationFactoryContract::class);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getAwsRequestId')
            ->willReturn('8476a536-e9f4-11e8-9739-2dfe598c3fcd');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);

        $mockedLambdaInvocationResponse
            ->expects(self::once())
            ->method('jsonSerialize')
            ->willReturn(new stdClass());

        $client = new Client($mockedHttpClient, $stubbedLambdaInvocationFactory);
        $client->sendInvocationResponse($mockedLambdaInvocation, $mockedLambdaInvocationResponse);

        self::assertSame('POST', $mockedResponse->getRequestMethod());
        self::assertStringEndsWith(
            '/2018-06-01/runtime/invocation/8476a536-e9f4-11e8-9739-2dfe598c3fcd/response',
            $mockedResponse->getRequestUrl()
        );
        self::assertContains('Content-Type: application/json', $mockedResponse->getRequestOptions()['headers']);
        self::assertSame('{}', $mockedResponse->getRequestOptions()['body']);
    }

    /** @test */
    public function it_sends_invocation_error(): void
    {
        $mockedResponse = new MockResponse('', ['http_code' => 202]);
        $mockedHttpClient = new MockHttpClient($mockedResponse);
        $mockedLambdaInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedLambdaInvocation = $this->createMock(LambdaInvocationContract::class);
        $stubbedLambdaInvocationFactory = $this->createStub(LambdaInvocationFactoryContract::class);

        $mockedLambdaInvocationContext
            ->expects(self::once())
            ->method('getAwsRequestId')
            ->willReturn('8476a536-e9f4-11e8-9739-2dfe598c3fcd');

        $mockedLambdaInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedLambdaInvocationContext);

        $exception = new Exception('Test error');

        $client = new Client($mockedHttpClient, $stubbedLambdaInvocationFactory);
        $client->sendInvocationError($mockedLambdaInvocation, $exception);

        self::assertSame('POST', $mockedResponse->getRequestMethod());
        self::assertStringEndsWith(
            '/2018-06-01/runtime/invocation/8476a536-e9f4-11e8-9739-2dfe598c3fcd/error',
            $mockedResponse->getRequestUrl()
        );
        self::assertContains(
            'Lambda-Runtime-Function-Error-Type: Unhandled',
            $mockedResponse->getRequestOptions()['headers']
        );
        self::assertContains('Content-Type: application/json', $mockedResponse->getRequestOptions()['headers']);
        self::assertStringStartsWith(
            '{"errorType":"Exception","errorMessage":"Test error","stackTrace":[',
            $mockedResponse->getRequestOptions()['body']
        );
    }

    /** @test */
    public function it_sends_initialisation_error(): void
    {
        $mockedResponse = new MockResponse('', ['http_code' => 202]);
        $mockedHttpClient = new MockHttpClient($mockedResponse);
        $exception = new Exception('Test error');
        $stubbedLambdaInvocationFactory = $this->createStub(LambdaInvocationFactoryContract::class);

        $client = new Client($mockedHttpClient, $stubbedLambdaInvocationFactory);
        $client->sendInitialisationError($exception);

        self::assertSame('POST', $mockedResponse->getRequestMethod());
        self::assertStringEndsWith('/2018-06-01/runtime/init/error', $mockedResponse->getRequestUrl());
        self::assertContains(
            'Lambda-Runtime-Function-Error-Type: Unhandled',
            $mockedResponse->getRequestOptions()['headers']
        );
        self::assertContains('Content-Type: application/json', $mockedResponse->getRequestOptions()['headers']);
        self::assertStringStartsWith(
            '{"errorType":"Exception","errorMessage":"Test error","stackTrace":[',
            $mockedResponse->getRequestOptions()['body']
        );
    }
}
