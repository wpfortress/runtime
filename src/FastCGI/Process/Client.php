<?php

declare(strict_types=1);

namespace WPFortress\Runtime\FastCGI\Process;

use hollodotme\FastCGI\Exceptions\TimedoutException;
use hollodotme\FastCGI\Interfaces\ConfiguresSocketConnection;
use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use Throwable;
use WPFortress\Runtime\Contracts\FastCGIProcessClientContract;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Exceptions\FastCGIProcessClientException;

final class Client implements FastCGIProcessClientContract
{
    public function __construct(
        private \hollodotme\FastCGI\Client $client,
        private ConfiguresSocketConnection $connection,
        private FastCGIProcessManagerContract $processManager,
    ) {
    }

    public function sendRequest(ProvidesRequestData $request, ?int $timeoutMs = null): ProvidesResponseData
    {
        try {
            $socketId = $this->client->sendAsyncRequest(
                connection: $this->connection,
                request: $request,
            );

            $response = $this->client->readResponse(
                socketId: $socketId,
                timeoutMs: $timeoutMs,
            );
        } catch (TimedoutException) {
            $this->restartFastCGIProcess();

            throw FastCGIProcessClientException::timedOut($timeoutMs);
        } catch (Throwable $exception) {
            $this->restartFastCGIProcess();

            throw FastCGIProcessClientException::communicationFailed($exception);
        }

        $this->processManager->ensureStillRunning();

        return $response;
    }

    private function restartFastCGIProcess(): void
    {
        $this->processManager->stop();
        $this->processManager->start();
    }
}
