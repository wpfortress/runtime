<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationEventContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpErrorResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpResponseFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionOneEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\APIGatewayVersionTwoEvent;
use WPFortress\Runtime\Lambda\Invocation\Events\ApplicationLoadBalancerEvent;
use WPFortress\Runtime\Lambda\Invocation\Responses\APIGatewayVersionOneResponse;
use WPFortress\Runtime\Lambda\Invocation\Responses\APIGatewayVersionTwoResponse;
use WPFortress\Runtime\Lambda\Invocation\Responses\ApplicationLoadBalancerResponse;
use WPFortress\Runtime\Lambda\Invocation\Responses\HttpResponseFactory;

final class HttpResponseFactoryTest extends TestCase
{
    /** @test */
    public function it_implements_lambda_invocation_http_response_factory_contract(): void
    {
        $httpResponseFactory = new HttpResponseFactory();

        self::assertInstanceOf(LambdaInvocationHttpResponseFactoryContract::class, $httpResponseFactory);
    }

    /** @test */
    public function it_throws_exception_for_unhandled_event_type_for_http_error_response(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unhandled Lambda event type.');

        $stubbedEvent = $this->createStub(LambdaInvocationEventContract::class);

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedEvent);

        $stubbedHttpErrorResponse = $this->createStub(LambdaInvocationHttpErrorResponseContract::class);

        $httpResponseFactory = new HttpResponseFactory();
        $httpResponseFactory->makeFromHttpErrorResponse($mockedInvocation, $stubbedHttpErrorResponse);
    }

    /** @test */
    public function it_makes_api_gateway_version_one_response_from_http_error_response(): void
    {
        $event = new APIGatewayVersionOneEvent(
            method: 'GET',
            path: '/',
            queryString: '',
            usesMultiValueHeaders: true,
            headers: [],
            isBase64Encoded: true,
            body: '',
        );

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $stubbedHttpErrorResponse = $this->createStub(LambdaInvocationHttpErrorResponseContract::class);

        $httpResponseFactory = new HttpResponseFactory();
        $response = $httpResponseFactory->makeFromHttpErrorResponse($mockedInvocation, $stubbedHttpErrorResponse);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(APIGatewayVersionOneResponse::class, $response);
    }

    /** @test */
    public function it_makes_api_gateway_version_two_response_from_http_error_response(): void
    {
        $event = new APIGatewayVersionTwoEvent(
            method: 'GET',
            path: '/',
            queryString: '',
            headers: [],
            isBase64Encoded: true,
            body: '',
        );

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $stubbedHttpErrorResponse = $this->createStub(LambdaInvocationHttpErrorResponseContract::class);

        $httpResponseFactory = new HttpResponseFactory();
        $response = $httpResponseFactory->makeFromHttpErrorResponse($mockedInvocation, $stubbedHttpErrorResponse);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(APIGatewayVersionTwoResponse::class, $response);
    }

    /** @test */
    public function it_makes_application_load_balancer_response_from_http_error_response(): void
    {
        $event = new ApplicationLoadBalancerEvent(
            method: 'GET',
            path: '/',
            queryString: '',
            usesMultiValueHeaders: true,
            headers: [],
            isBase64Encoded: true,
            body: '',
        );

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $stubbedHttpErrorResponse = $this->createStub(LambdaInvocationHttpErrorResponseContract::class);

        $httpResponseFactory = new HttpResponseFactory();
        $response = $httpResponseFactory->makeFromHttpErrorResponse($mockedInvocation, $stubbedHttpErrorResponse);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(ApplicationLoadBalancerResponse::class, $response);
    }

    /** @test */
    public function it_throws_exception_for_unhandled_event_type_for_fastcgi_response(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unhandled Lambda event type.');

        $stubbedEvent = $this->createStub(LambdaInvocationEventContract::class);

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($stubbedEvent);

        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);

        $httpResponseFactory = new HttpResponseFactory();
        $httpResponseFactory->makeFromFastCGIResponse($mockedInvocation, $stubbedFastCGIResponse);
    }

    /** @test */
    public function it_makes_api_gateway_version_one_response_from_fastcgi_response(): void
    {
        $event = new APIGatewayVersionOneEvent(
            method: 'GET',
            path: '/',
            queryString: '',
            usesMultiValueHeaders: true,
            headers: [],
            isBase64Encoded: true,
            body: '',
        );

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);

        $httpResponseFactory = new HttpResponseFactory();
        $response = $httpResponseFactory->makeFromFastCGIResponse($mockedInvocation, $stubbedFastCGIResponse);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(APIGatewayVersionOneResponse::class, $response);
    }

    /** @test */
    public function it_makes_api_gateway_version_two_response_from_fastcgi_response(): void
    {
        $event = new APIGatewayVersionTwoEvent(
            method: 'GET',
            path: '/',
            queryString: '',
            headers: [],
            isBase64Encoded: true,
            body: '',
        );

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);

        $httpResponseFactory = new HttpResponseFactory();
        $response = $httpResponseFactory->makeFromFastCGIResponse($mockedInvocation, $stubbedFastCGIResponse);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(APIGatewayVersionTwoResponse::class, $response);
    }

    /** @test */
    public function it_makes_application_load_balancer_response_from_fastcgi_response(): void
    {
        $event = new ApplicationLoadBalancerEvent(
            method: 'GET',
            path: '/',
            queryString: '',
            usesMultiValueHeaders: true,
            headers: [],
            isBase64Encoded: true,
            body: '',
        );

        $mockedInvocation = $this->createMock(LambdaInvocationContract::class);
        $mockedInvocation
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);

        $httpResponseFactory = new HttpResponseFactory();
        $response = $httpResponseFactory->makeFromFastCGIResponse($mockedInvocation, $stubbedFastCGIResponse);

        self::assertInstanceOf(LambdaInvocationResponseContract::class, $response);
        self::assertInstanceOf(ApplicationLoadBalancerResponse::class, $response);
    }
}
