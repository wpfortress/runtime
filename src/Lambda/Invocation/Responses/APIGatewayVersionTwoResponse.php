<?php

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use JsonSerializable;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class APIGatewayVersionTwoResponse implements InvocationResponseContract, JsonSerializable
{
    /**
     * @param list<string> $cookies
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $body,
        private bool $isBase64Encoded = false,
        private array $cookies = [],
        private array $headers = [],
        private int $status = HttpStatus::OK,
    ) {
    }

    /**
     * @return array{
     *  isBase64Encoded: bool,
     *  statusCode: int,
     *  cookies: list<string>,
     *  headers: stdClass|array<string, string>,
     *  body: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'isBase64Encoded' => $this->isBase64Encoded,
            'statusCode' => $this->status,
            'cookies' => $this->cookies,
            'headers' => $this->headers !== [] ? $this->headers : new stdClass(),
            'body' => $this->isBase64Encoded ? base64_encode($this->body) : $this->body,
        ];
    }
}
