<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\FastCGI\Process;

use Exception;
use hollodotme\FastCGI\Exceptions\TimedoutException;
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;
use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Contracts\FastCGIProcessClientContract;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Exceptions\FastCGIProcessClientException;
use WPFortress\Runtime\FastCGI\Process\Client;

final class ClientTest extends TestCase
{
    /** @test */
    public function it_implements_fast_cgi_process_client_contract(): void
    {
        $stubbedFastCGIClient = $this->createStub(\hollodotme\FastCGI\Client::class);
        $stubbedFastCGIConnection = $this->createStub(ConfiguresSocketConnection::class);
        $stubbedFastCGIProcessManager = $this->createStub(FastCGIProcessManagerContract::class);

        $client = new Client(
            client: $stubbedFastCGIClient,
            connection: $stubbedFastCGIConnection,
            processManager: $stubbedFastCGIProcessManager,
        );

        self::assertInstanceOf(FastCGIProcessClientContract::class, $client);
    }

    /** @test */
    public function it_throws_timed_out_exception(): void
    {
        $this->expectException(FastCGIProcessClientException::class);
        $this->expectExceptionMessageMatches('/timed out/');

        $mockedFastCGIClient = $this->createMock(\hollodotme\FastCGI\Client::class);
        $stubbedFastCGIConnection = $this->createStub(ConfiguresSocketConnection::class);
        $mockedFastCGIProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);

        $mockedFastCGIClient
            ->expects(self::once())
            ->method('sendAsyncRequest')
            ->with(self::equalTo($stubbedFastCGIConnection), self::equalTo($stubbedFastCGIRequest))
            ->willReturn(100);
        $mockedFastCGIClient
            ->expects(self::once())
            ->method('readResponse')
            ->with(self::equalTo(100), self::equalTo(null))
            ->willThrowException(new TimedoutException('foo'));

        $mockedFastCGIProcessManager
            ->expects(self::once())
            ->method('stop');
        $mockedFastCGIProcessManager
            ->expects(self::once())
            ->method('start');

        $client = new Client(
            client: $mockedFastCGIClient,
            connection: $stubbedFastCGIConnection,
            processManager: $mockedFastCGIProcessManager,
        );
        $client->sendRequest($stubbedFastCGIRequest);
    }

    /** @test */
    public function it_throws_communication_failed_exception(): void
    {
        $this->expectException(FastCGIProcessClientException::class);
        $this->expectExceptionMessageMatches('/response/');

        $mockedFastCGIClient = $this->createMock(\hollodotme\FastCGI\Client::class);
        $stubbedFastCGIConnection = $this->createStub(ConfiguresSocketConnection::class);
        $mockedFastCGIProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);

        $mockedFastCGIClient
            ->expects(self::once())
            ->method('sendAsyncRequest')
            ->with(self::equalTo($stubbedFastCGIConnection), self::equalTo($stubbedFastCGIRequest))
            ->willReturn(100);
        $mockedFastCGIClient
            ->expects(self::once())
            ->method('readResponse')
            ->with(self::equalTo(100), self::equalTo(null))
            ->willThrowException(new Exception('foo'));

        $mockedFastCGIProcessManager
            ->expects(self::once())
            ->method('stop');
        $mockedFastCGIProcessManager
            ->expects(self::once())
            ->method('start');

        $client = new Client(
            client: $mockedFastCGIClient,
            connection: $stubbedFastCGIConnection,
            processManager: $mockedFastCGIProcessManager,
        );
        $client->sendRequest($stubbedFastCGIRequest);
    }

    /** @test */
    public function it_sends_given_fastcgi_request(): void
    {
        $mockedFastCGIClient = $this->createMock(\hollodotme\FastCGI\Client::class);
        $stubbedFastCGIConnection = $this->createStub(ConfiguresSocketConnection::class);
        $mockedProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $stubbedFastCGIRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedFastCGIResponse = $this->createStub(ProvidesResponseData::class);

        $mockedFastCGIClient
            ->expects(self::once())
            ->method('sendAsyncRequest')
            ->with(self::equalTo($stubbedFastCGIConnection), self::equalTo($stubbedFastCGIRequest))
            ->willReturn(100);
        $mockedFastCGIClient
            ->expects(self::once())
            ->method('readResponse')
            ->with(self::equalTo(100), self::equalTo(null))
            ->willReturn($stubbedFastCGIResponse);

        $mockedProcessManager
            ->expects(self::once())
            ->method('ensureStillRunning');

        $client = new Client(
            client: $mockedFastCGIClient,
            connection: $stubbedFastCGIConnection,
            processManager: $mockedProcessManager,
        );
        $response = $client->sendRequest($stubbedFastCGIRequest);

        self::assertInstanceOf(ProvidesResponseData::class, $response);
    }
}
