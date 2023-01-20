<?php

declare(strict_types=1);

namespace WPFortress\Runtime\FastCGI\Process;

use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Exceptions\FastCGIProcessManagerException;

final class Manager implements FastCGIProcessManagerContract
{
    private const SIGTERM = 15;

    public static function fromConfig(string $configPath, string $socketPath, string $pidPath): self
    {
        return new self(
            process: new Process(['php-fpm', '--nodaemonize', '--force-stderr', '--fpm-config', $configPath]),
            socketPath: $socketPath,
            pidPath: $pidPath,
        );
    }

    public function __construct(
        private Process $process,
        private string $socketPath,
        private string $pidPath,
    ) {
    }

    public function __destruct()
    {
        $this->stop();
    }

    public function start(): void
    {
        if ($this->isReady()) {
            $this->killExistingProcess();
        }

        $directory = dirname($this->socketPath);
        if (!is_dir($directory)) {
            mkdir($directory);
        }

        $this->process->setTimeout(null);
        $this->process->start(function ($type, $output): void {
            echo $output;
        });

        $this->waitUntilReady();
    }

    public function stop(): void
    {
        if (!$this->process->isRunning()) {
            return;
        }

        $this->process->stop(0.5);

        if ($this->isReady()) {
            throw FastCGIProcessManagerException::cannotBeStopped();
        }
    }

    public function ensureStillRunning(): void
    {
        if ($this->process->isRunning()) {
            return;
        }

        throw FastCGIProcessManagerException::unknownStop();
    }

    private function isReady(): bool
    {
        clearstatcache(false, $this->socketPath);

        return file_exists($this->socketPath);
    }

    private function waitUntilReady(): void
    {
        $wait = 5000; // 5ms
        $timeout = 5000000; // 5 secs
        $elapsed = 0;

        while (!$this->isReady()) {
            usleep($wait);

            $elapsed += $wait;
            if ($elapsed > $timeout) {
                throw FastCGIProcessManagerException::startTimeout();
            }

            if (!$this->process->isRunning()) {
                throw FastCGIProcessManagerException::startFailure($this->process);
            }
        }
    }

    private function waitUntilStopped(int $pid): void
    {
        $wait = 5000; // 5ms
        $timeout = 1000000; // 1 sec
        $elapsed = 0;

        while (posix_getpgid($pid) !== false) {
            usleep($wait);

            $elapsed += $wait;
            if ($elapsed > $timeout) {
                throw FastCGIProcessManagerException::stopTimeout();
            }
        }
    }

    private function killExistingProcess(): void
    {
        if (!file_exists($this->pidPath)) {
            unlink($this->socketPath);
            return;
        }

        $pid = (int)file_get_contents($this->pidPath);

        if ($pid <= 0) {
            $this->removeProcessFiles();
            return;
        }

        if (posix_getpgid($pid) === false) {
            $this->removeProcessFiles();
            return;
        }

        if ($pid === posix_getpid()) {
            $this->removeProcessFiles();
            return;
        }

        if (posix_kill($pid, self::SIGTERM) === false) {
            $this->removeProcessFiles();
            return;
        }

        $this->waitUntilStopped($pid);

        $this->removeProcessFiles();
    }

    private function removeProcessFiles(): void
    {
        unlink($this->socketPath);
        unlink($this->pidPath);
    }
}
