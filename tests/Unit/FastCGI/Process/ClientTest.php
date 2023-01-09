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
use WPFortress\Runtime\FastCGI\Process\Client;

final class ClientTest extends TestCase
{
    /** @test */
    public function it_implements_fast_cgi_process_client_contract(): void
    {
        $stubbedClient = $this->createStub(\hollodotme\FastCGI\Client::class);
        $stubbedConnection = $this->createStub(ConfiguresSocketConnection::class);
        $stubbedProcessManager = $this->createStub(FastCGIProcessManagerContract::class);

        $processClient = new Client(
            client: $stubbedClient,
            connection: $stubbedConnection,
            processManager: $stubbedProcessManager,
        );

        self::assertInstanceOf(FastCGIProcessClientContract::class, $processClient);
    }

    /** @test */
    public function it_sends_given_request(): void
    {
        $stubbedRequest = $this->createStub(ProvidesRequestData::class);
        $stubbedResponse = $this->createStub(ProvidesResponseData::class);

        $stubbedConnection = $this->createStub(ConfiguresSocketConnection::class);

        $mockedClient = $this->createMock(\hollodotme\FastCGI\Client::class);
        $mockedClient
            ->expects(self::once())
            ->method('sendAsyncRequest')
            ->with($stubbedConnection, $stubbedRequest)
            ->willReturn(100);
        $mockedClient
            ->expects(self::once())
            ->method('readResponse')
            ->with(100, null)
            ->willReturn($stubbedResponse);

        $mockedProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $mockedProcessManager
            ->expects(self::once())
            ->method('ensureStillRunning');

        $processClient = new Client(
            client: $mockedClient,
            connection: $stubbedConnection,
            processManager: $mockedProcessManager,
        );
        $response = $processClient->sendRequest($stubbedRequest);

        self::assertInstanceOf(ProvidesResponseData::class, $response);
    }

    /** @test */
    public function it_throws_timedout_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PHP script timed out.');

        $stubbedRequest = $this->createStub(ProvidesRequestData::class);

        $stubbedConnection = $this->createStub(ConfiguresSocketConnection::class);

        $mockedClient = $this->createMock(\hollodotme\FastCGI\Client::class);
        $mockedClient
            ->expects(self::once())
            ->method('sendAsyncRequest')
            ->with($stubbedConnection, $stubbedRequest)
            ->willReturn(100);
        $mockedClient
            ->expects(self::once())
            ->method('readResponse')
            ->with(100, null)
            ->willThrowException(new TimedoutException('foo'));

        $mockedProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $mockedProcessManager->expects(self::once())->method('stop');
        $mockedProcessManager->expects(self::once())->method('start');

        $processClient = new Client(
            client: $mockedClient,
            connection: $stubbedConnection,
            processManager: $mockedProcessManager,
        );
        $processClient->sendRequest($stubbedRequest);
    }

    /** @test */
    public function it_throws_custom_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to read a response from PHP-FPM.');

        $stubbedRequest = $this->createStub(ProvidesRequestData::class);

        $stubbedConnection = $this->createStub(ConfiguresSocketConnection::class);

        $mockedClient = $this->createMock(\hollodotme\FastCGI\Client::class);
        $mockedClient
            ->expects(self::once())
            ->method('sendAsyncRequest')
            ->with($stubbedConnection, $stubbedRequest)
            ->willReturn(100);
        $mockedClient
            ->expects(self::once())
            ->method('readResponse')
            ->with(100, null)
            ->willThrowException(new Exception('foo'));

        $mockedProcessManager = $this->createMock(FastCGIProcessManagerContract::class);
        $mockedProcessManager->expects(self::once())->method('stop');
        $mockedProcessManager->expects(self::once())->method('start');

        $processClient = new Client(
            client: $mockedClient,
            connection: $stubbedConnection,
            processManager: $mockedProcessManager,
        );
        $processClient->sendRequest($stubbedRequest);
    }
}
