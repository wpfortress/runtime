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
        $stubbedInvocationFactory = $this->createStub(LambdaInvocationFactoryContract::class);

        $client = new Client($stubbedHttpClient, $stubbedInvocationFactory);

        self::assertInstanceOf(LambdaRuntimeClientContract::class, $client);
    }

    /** @test */
    public function it_retrieves_next_invocation(): void
    {
        $mockedResponse = new MockResponse('', ['http_code' => 200]);
        $mockedHttpClient = new MockHttpClient($mockedResponse);

        $stubbedInvocation = $this->createStub(LambdaInvocationContract::class);

        $mockedInvocationFactory = $this->createMock(LambdaInvocationFactoryContract::class);
        $mockedInvocationFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn($stubbedInvocation);

        $client = new Client($mockedHttpClient, $mockedInvocationFactory);
        $invocation = $client->retrieveNextInvocation();

        self::assertSame('GET', $mockedResponse->getRequestMethod());
        self::assertStringEndsWith('/2018-06-01/runtime/invocation/next', $mockedResponse->getRequestUrl());
        self::assertSame($stubbedInvocation, $invocation);
    }

    /** @test */
    public function it_sends_invocation_response(): void
    {
        $mockedResponse = new MockResponse('', ['http_code' => 202]);
        $mockedHttpClient = new MockHttpClient($mockedResponse);

        $mockedInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedInvocationContext
            ->expects(self::once())
            ->method('getAwsRequestId')
            ->willReturn('8476a536-e9f4-11e8-9739-2dfe598c3fcd');

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedInvocationContext);

        $mockedInvocationResponse = $this->createMock(LambdaInvocationResponseContract::class);
        $mockedInvocationResponse
            ->expects(self::once())
            ->method('jsonSerialize')
            ->willReturn(new stdClass());

        $stubbedInvocationFactoryContract = $this->createStub(LambdaInvocationFactoryContract::class);

        $client = new Client($mockedHttpClient, $stubbedInvocationFactoryContract);
        $client->sendInvocationResponse($mockedInvocation, $mockedInvocationResponse);

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

        $mockedInvocationContext = $this->createMock(LambdaInvocationContextContract::class);
        $mockedInvocationContext
            ->expects(self::once())
            ->method('getAwsRequestId')
            ->willReturn('8476a536-e9f4-11e8-9739-2dfe598c3fcd');

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getContext')
            ->willReturn($mockedInvocationContext);

        $exception = new Exception('Test error');

        $stubbedInvocationFactoryContract = $this->createStub(LambdaInvocationFactoryContract::class);

        $client = new Client($mockedHttpClient, $stubbedInvocationFactoryContract);
        $client->sendInvocationError($mockedInvocation, $exception);

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

        $stubbedInvocationFactoryContract = $this->createStub(LambdaInvocationFactoryContract::class);

        $client = new Client($mockedHttpClient, $stubbedInvocationFactoryContract);
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
