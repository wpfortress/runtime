<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface FastCGIProcessManagerContract
{
    public function start(): void;

    public function stop(): void;

    public function ensureStillRunning(): void;
}
