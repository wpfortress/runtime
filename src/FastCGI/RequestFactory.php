<?php

declare(strict_types=1);

namespace WPFortress\Runtime\FastCGI;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use WPFortress\Runtime\Contracts\FastCGIRequestFactoryContract;
use WPFortress\Runtime\Contracts\LambdaInvocationContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;

final class RequestFactory implements FastCGIRequestFactoryContract
{
    public function make(LambdaInvocationContract $invocation, string $scriptFilename): ProvidesRequestData
    {
        assert($invocation->getEvent() instanceof LambdaInvocationHttpEventContract);

        /** @var LambdaInvocationHttpEventContract $event */
        $event = $invocation->getEvent();

        $path = $event->getPath();
        $queryString = $event->getQueryString();
        $headers = $event->getHeaders();

        $documentRoot = getcwd();
        $documentRoot = $documentRoot !== false ? $documentRoot : '';

        $scriptName = str_replace($documentRoot, '', $scriptFilename);

        $pathInfo = '';
        $phpSelf = $scriptName . $path;
        if (preg_match('%^(.+\.php)(/.+)$%i', $path, $matches) === 1) {
            $pathInfo = $matches[2];
            $phpSelf = $matches[0];
        }

        $request = new Request($scriptFilename, $event->getBody());
        $request->setRequestMethod($event->getRequestMethod());
        $request->setRequestUri($queryString === '' ? $path : $path . '?' . $queryString);
        $request->setRemoteAddress($headers['x-forwarded-for'][0] ?? '127.0.0.1');
        $request->setRemotePort((int)($headers['x-forwarded-port'][0] ?? 80));
        $request->setServerAddress('127.0.0.1');
        $request->setServerPort((int)($headers['x-forwarded-port'][0] ?? 80));
        $request->setServerName($headers['x-forwarded-host'][0] ?? $headers['host'][0] ?? 'localhost');
        $request->setServerSoftware('WPFortress');
        $request->setCustomVar('REQUEST_TIME', time());
        $request->setCustomVar('REQUEST_TIME_FLOAT', microtime(true));
        $request->setCustomVar('DOCUMENT_ROOT', $documentRoot);
        $request->setCustomVar('PATH_INFO', $pathInfo);
        $request->setCustomVar('PHP_SELF', '/' . trim($phpSelf, '/'));
        $request->setCustomVar('SCRIPT_NAME', $scriptName);
        $request->setCustomVar('QUERY_STRING', $queryString);
        $request->setCustomVar(
            'LAMBDA_INVOCATION_CONTEXT',
            json_encode($invocation->getContext(), JSON_THROW_ON_ERROR)
        );

        if (isset($headers['x-forwarded-proto'][0]) && strtolower($headers['x-forwarded-proto'][0]) === 'https') {
            $request->setCustomVar('HTTPS', 'on');
        }

        foreach ($headers as $header => $values) {
            $request->setCustomVar(
                'HTTP_' . strtoupper(str_replace('-', '_', $header)),
                end($values)
            );
        }

        $request->setCustomVar('HTTP_HOST', $request->getServerName());

        return $request;
    }
}
