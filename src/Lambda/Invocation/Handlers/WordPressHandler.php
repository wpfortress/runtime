<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHandlerContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;

final class WordPressHandler extends AbstractPhpFpmHandler implements LambdaInvocationHandlerContract
{
    public function shouldHandle(LambdaInvocationContract $invocation): bool
    {
        return parent::shouldHandle($invocation)
            && file_exists($this->lambdaRootDirectory . '/wp-config.php')
            && file_exists($this->lambdaRootDirectory . '/index.php');
    }

    protected function resolveRequestedFilenameFrom(LambdaInvocationHttpEventContract $event): string
    {
        $path = $event->getPath();

        if (preg_match('%^(.+\.php)%i', $path, $matches) === 1) {
            $path = $matches[1];
        }

        if (
            $this->isMultisite() &&
            (
                preg_match('/^(.*)?(\/wp-(content|admin|includes).*)/', $path, $matches) === 1 ||
                preg_match('/^(.*)?(\/.*\.php)/', $path, $matches) === 1
            )
        ) {
            $path = $matches[2];
        }

        return $this->lambdaRootDirectory . '/' . ltrim($path, '/');
    }

    protected function resolveScriptFilenameFrom(LambdaInvocationHttpEventContract $event): string
    {
        $requestedFilename = $this->resolveRequestedFilenameFrom($event);

        if (is_dir($requestedFilename)) {
            $requestedFilename = rtrim($requestedFilename, '/') . '/index.php';
        }

        return file_exists($requestedFilename)
            ? $requestedFilename
            : $this->lambdaRootDirectory . '/index.php';
    }

    protected function isPubliclyAccessible(string $filename): bool
    {
        return preg_match('/(wp-config\.php|readme\.html|license\.txt|wp-cli\.yml)$/', $filename) === 0;
    }

    protected function isMultisite(): bool
    {
        $config = file_get_contents($this->lambdaRootDirectory . '/wp-config.php');

        return is_string($config)
            && preg_match('/define\(\s*(\'|\")MULTISITE\1\s*,\s*true\s*\)/', $config) === 1;
    }
}
