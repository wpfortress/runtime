<?php

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use JsonSerializable;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationHttpErrorResponseContract;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class ApplicationLoadBalancerResponse implements InvocationResponseContract, JsonSerializable
{
    public static function fromFastCGIResponse(ProvidesResponseData $response): self
    {
        $headers = $response->getHeaders();

        $status = (int)($headers['Status'][0] ?? HttpStatus::OK);
        unset($headers['Status']);

        return new self(
            body: $response->getBody(),
            multiValueHeaders: $headers,
            status: $status,
        );
    }

    public static function fromHttpErrorResponse(InvocationHttpErrorResponseContract $response): self
    {
        return new self(
            body: $response->getBody(),
            multiValueHeaders: $response->getHeaders(),
            status: $response->getStatus(),
        );
    }

    /** @param array<string, list<string>> $multiValueHeaders */
    public function __construct(
        private string $body,
        private bool $isBase64Encoded = false,
        private array $multiValueHeaders = [],
        private int $status = HttpStatus::OK,
    ) {
    }

    /**
     * @return array{
     *  isBase64Encoded: bool,
     *  statusCode: int,
     *  statusDescription: string,
     *  multiValueHeaders: stdClass|array<string, list<string>>,
     *  body: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'isBase64Encoded' => $this->isBase64Encoded,
            'statusCode' => $this->status,
            'statusDescription' => $this->status . ' ' . HttpStatus::TEXTS[$this->status],
            'multiValueHeaders' => $this->multiValueHeaders !== [] ? $this->multiValueHeaders : new stdClass(),
            'body' => $this->isBase64Encoded ? base64_encode($this->body) : $this->body,
        ];
    }
}
