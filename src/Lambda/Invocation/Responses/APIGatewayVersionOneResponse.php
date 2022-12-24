<?php

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use JsonSerializable;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationResponseContract;

final class APIGatewayVersionOneResponse implements InvocationResponseContract, JsonSerializable
{
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
     *  multiValueHeaders: stdClass|array<string, list<string>>,
     *  body: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'isBase64Encoded' => $this->isBase64Encoded,
            'statusCode' => $this->status,
            'multiValueHeaders' => $this->multiValueHeaders !== [] ? $this->multiValueHeaders : new stdClass(),
            'body' => $this->isBase64Encoded ? base64_encode($this->body) : $this->body,
        ];
    }
}
