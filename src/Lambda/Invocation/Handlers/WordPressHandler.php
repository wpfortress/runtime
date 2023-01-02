<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Handlers;

use WPFortress\Runtime\Contracts\InvocationContract;
use WPFortress\Runtime\Contracts\InvocationHandlerContract;

final class WordPressHandler extends AbstractPhpFpmHandler implements InvocationHandlerContract
{
    public function shouldHandle(InvocationContract $invocation): bool
    {
        return parent::shouldHandle($invocation)
            && file_exists($this->rootDirectory . '/wp-config.php')
            && file_exists($this->rootDirectory . '/index.php');
    }

    protected function resolveRequestedFilenameFrom(InvocationContract $invocation): string
    {
        $path = $invocation->getEvent()->getPath();

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

        return $this->rootDirectory . '/' . ltrim($path, '/');
    }

    protected function resolveScriptFilenameFrom(InvocationContract $invocation): string
    {
        $requestedFilename = $this->resolveRequestedFilenameFrom($invocation);

        if (is_dir($requestedFilename)) {
            $requestedFilename = rtrim($requestedFilename, '/') . '/index.php';
        }

        return file_exists($requestedFilename)
            ? $requestedFilename
            : $this->rootDirectory . '/index.php';
    }

    protected function isPubliclyAccessible(string $filename): bool
    {
        return preg_match('/(wp-config\.php|readme\.html|license\.txt|wp-cli\.yml)$/', $filename) === 0;
    }

    protected function isMultisite(): bool
    {
        $config = file_get_contents($this->rootDirectory . '/wp-config.php');

        return is_string($config)
            && preg_match('/define\(\s*(\'|\")MULTISITE\1\s*,\s*true\s*\)/', $config) === 1;
    }
}
