<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use hollodotme\FastCGI\Interfaces\ProvidesResponseData;
use stdClass;
use WPFortress\Runtime\Constants\HttpStatus;
use WPFortress\Runtime\Contracts\LambdaInvocationHttpErrorResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationResponseContract;
use WPFortress\Runtime\Contracts\LambdaInvocationStaticFileResponseContract;

final class APIGatewayVersionOneResponse implements LambdaInvocationResponseContract
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

    public static function fromHttpErrorResponse(LambdaInvocationHttpErrorResponseContract $response): self
    {
        return new self(
            body: $response->getBody(),
            multiValueHeaders: $response->getHeaders(),
            status: $response->getStatus(),
        );
    }

    public static function fromStaticResponse(LambdaInvocationStaticFileResponseContract $response): self
    {
        return new self(
            body: $response->getBody(),
            isBase64Encoded: true,
            multiValueHeaders: $response->getHeaders(),
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
