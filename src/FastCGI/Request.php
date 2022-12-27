<?php

declare(strict_types=1);

namespace WPFortress\Runtime\FastCGI;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;

final class Request implements ProvidesRequestData
{
    public const DEFAULT_GATEWAY_INTERFACE = 'FastCGI/1.0';

    public const DEFAULT_REQUEST_METHOD = 'GET';

    public const DEFAULT_SERVER_SOFTWARE = 'WPFortress';

    public const DEFAULT_REMOTE_ADDR = '192.168.0.1';
    public const DEFAULT_REMOTE_PORT = 9985;

    public const DEFAULT_SERVER_ADDR = '127.0.0.1';
    public const DEFAULT_SERVER_PORT = 80;
    public const DEFAULT_SERVER_NAME = 'localhost';
    public const DEFAULT_SERVER_PROTOCOL = 'HTTP/1.1';

    public const DEFAULT_CONTENT_TYPE = 'application/x-www-form-urlencoded';
    public const DEFAULT_CONTENT_LENGTH = 0;

    /**
     * @param array{
     *  GATEWAY_INTERFACE: string,
     *  REQUEST_METHOD: string,
     *  REQUEST_URI: string,
     *  SCRIPT_FILENAME: string,
     *  SERVER_SOFTWARE: string,
     *  REMOTE_ADDR: string,
     *  REMOTE_PORT: int,
     *  SERVER_ADDR: string,
     *  SERVER_PORT: int,
     *  SERVER_NAME: string,
     *  SERVER_PROTOCOL: string,
     *  CONTENT_TYPE: string,
     *  CONTENT_LENGTH: int,
     * } $parameters
     */
    public function __construct(
        private string $content,
        private array $parameters,
    ) {
    }

    public function getGatewayInterface(): string
    {
        return $this->parameters['GATEWAY_INTERFACE'] ?? self::DEFAULT_GATEWAY_INTERFACE;
    }

    public function getRequestMethod(): string
    {
        return $this->parameters['REQUEST_METHOD'] ?? self::DEFAULT_REQUEST_METHOD;
    }

    public function getScriptFilename(): string
    {
        return $this->parameters['SCRIPT_FILENAME'] ?? '';
    }

    public function getServerSoftware(): string
    {
        return $this->parameters['SERVER_SOFTWARE'] ?? self::DEFAULT_SERVER_SOFTWARE;
    }

    public function getRemoteAddress(): string
    {
        return $this->parameters['REMOTE_ADDR'] ?? self::DEFAULT_REMOTE_ADDR;
    }

    public function getRemotePort(): int
    {
        return $this->parameters['REMOTE_PORT'] ?? self::DEFAULT_REMOTE_PORT;
    }

    public function getServerAddress(): string
    {
        return $this->parameters['SERVER_ADDR'] ?? self::DEFAULT_SERVER_ADDR;
    }

    public function getServerPort(): int
    {
        return $this->parameters['SERVER_PORT'] ?? self::DEFAULT_SERVER_PORT;
    }

    public function getServerName(): string
    {
        return $this->parameters['SERVER_NAME'] ?? self::DEFAULT_SERVER_NAME;
    }

    public function getServerProtocol(): string
    {
        return $this->parameters['SERVER_PROTOCOL'] ?? self::DEFAULT_SERVER_PROTOCOL;
    }

    public function getContentType(): string
    {
        return $this->parameters['CONTENT_TYPE'] ?? self::DEFAULT_CONTENT_TYPE;
    }

    public function getContentLength(): int
    {
        return $this->parameters['CONTENT_LENGTH'] ?? self::DEFAULT_CONTENT_LENGTH;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCustomVars(): array
    {
        return $this->parameters;
    }

    public function getParams(): array
    {
        return $this->parameters;
    }

    public function getRequestUri(): string
    {
        return $this->parameters['REQUEST_URI'] ?? '';
    }

    public function getResponseCallbacks(): array
    {
        return [];
    }

    public function getFailureCallbacks(): array
    {
        return [];
    }

    public function getPassThroughCallbacks(): array
    {
        return [];
    }
}
