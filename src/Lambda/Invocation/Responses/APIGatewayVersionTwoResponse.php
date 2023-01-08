<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use JsonSerializable;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\InvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpErrorResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationStaticFileResponseContract;

final class APIGatewayVersionTwoResponse implements InvocationResponseContract, JsonSerializable
{
    public static function fromFastCGIResponse(ProvidesResponseData $response): self
    {
        $cookies = $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            if (strtolower($name) === 'set-cookie') {
                $cookies[] = (string)end($values);
            } else {
                $headers[$name] = (string)end($values);
            }
        }

        $status = (int)($headers['Status'] ?? HttpStatus::OK);
        unset($headers['Status']);

        return new self(
            body: $response->getBody(),
            cookies: $cookies,
            headers: $headers,
            status: $status,
        );
    }

    public static function fromHttpErrorResponse(LambdaInvocationHttpErrorResponseContract $response): self
    {
        return new self(
            body: $response->getBody(),
            headers: array_map(fn(array $values): string => (string)end($values), $response->getHeaders()),
            status: $response->getStatus(),
        );
    }

    public static function fromStaticResponse(LambdaInvocationStaticFileResponseContract $response): self
    {
        return new self(
            body: $response->getBody(),
            isBase64Encoded: true,
            headers: array_map(fn(array $values): string => (string)end($values), $response->getHeaders()),
        );
    }

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
