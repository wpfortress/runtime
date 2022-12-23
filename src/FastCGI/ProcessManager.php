<?php

declare(strict_types=1);

namespace WPFortress\Runtime\FastCGI;

use Exception;
use Symfony\Component\Process\Process;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;

final class ProcessManager implements FastCGIProcessManagerContract
{
    private const SOCKET_PATH = '/tmp/.wpfortress/php-fpm.sock';

    private const PID_PATH = '/tmp/.wpfortress/php-fpm.pid';

    private const CONFIG_PATH = '/opt/wpfortress/etc/php-fpm.conf';

    private const SIGTERM = 15;

    public static function fromConfig(string $configPath = self::CONFIG_PATH): self
    {
        return new self(
            process: new Process(['php-fpm', '--nodaemonize', '--force-stderr', '--fpm-config', $configPath]),
        );
    }

    public function __construct(
        private Process $process,
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

        $directory = dirname(self::SOCKET_PATH);
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
            throw new Exception('PHP-FPM cannot be stopped.');
        }
    }

    public function ensureStillRunning(): void
    {
        if ($this->process->isRunning()) {
            return;
        }

        throw new Exception('PHP-FPM has stopped for an unknown reason.');
    }

    private function isReady(): bool
    {
        clearstatcache(false, self::SOCKET_PATH);

        return file_exists(self::SOCKET_PATH);
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
                throw new Exception('Timeout while waiting for PHP-FPM to start.');
            }

            if (!$this->process->isRunning()) {
                throw new Exception(
                    'PHP-FPM failed to start: ' . PHP_EOL .
                    $this->process->getOutput() . PHP_EOL .
                    $this->process->getErrorOutput()
                );
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
                throw new Exception('Timeout while waiting for PHP-FPM to stop.');
            }
        }
    }

    private function killExistingProcess(): void
    {
        if (!file_exists(self::PID_PATH)) {
            unlink(self::SOCKET_PATH);
            return;
        }

        $pid = (int)file_get_contents(self::PID_PATH);

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
        unlink(self::SOCKET_PATH);
        unlink(self::PID_PATH);
    }
}
