<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;
use WPFortress\Runtime\Contracts\InvocationHttpEventContract;

final class APIGatewayVersionTwoEvent implements InvocationEventContract, InvocationHttpEventContract
{
    /**
     * @param array{
     *  rawPath: string,
     *  rawQueryString: string,
     *  cookies: array<string, string>,
     *  headers: array<string, string>,
     *  requestContext: array{
     *   http: array{
     *    method: string,
     *   },
     *  },
     *  isBase64Encoded: bool,
     *  body: string,
     * } $data
     */
    public static function fromResponseData(array $data): self
    {
        return new self(
            method: strtoupper($data['requestContext']['http']['method']),
            path: $data['rawPath'],
            queryString: self::resolveQueryStringFrom($data),
            headers: self::resolveHeadersFrom($data),
            isBase64Encoded: $data['isBase64Encoded'],
            body: self::resolveBodyFrom($data),
        );
    }

    /**
     * @param array{
     *  rawQueryString: string,
     * } $data
     */
    private static function resolveQueryStringFrom(array $data): string
    {
        parse_str($data['rawQueryString'], $decodedQueryParameters);

        return http_build_query($decodedQueryParameters);
    }

    /**
     * @param array{
     *  cookies: array<string, string>,
     *  headers: array<string, string>,
     *  isBase64Encoded: bool,
     *  body: string,
     * } $data
     * @return array<string, list<string>>
     */
    private static function resolveHeadersFrom(array $data): array
    {
        $headers = array_map(fn(string $value): array => [$value], $data['headers']);
        $headers = array_change_key_case($headers, CASE_LOWER);

        if ($data['cookies'] !== []) {
            $headers['cookie'] = [implode('; ', $data['cookies'])];
        }

        $hasBody = $data['body'] !== '';

        if ($hasBody && !isset($headers['content-type'])) {
            $headers['content-type'] = ['application/x-www-form-urlencoded'];
        }

        if ($hasBody && !isset($headers['content-length'])) {
            $headers['content-length'] = [(string)strlen(self::resolveBodyFrom($data))];
        }

        return $headers;
    }

    /**
     * @param array{
     *  isBase64Encoded: bool,
     *  body: string,
     * } $data
     */
    private static function resolveBodyFrom(array $data): string
    {
        if ($data['isBase64Encoded'] === false) {
            return $data['body'];
        }

        $body = base64_decode($data['body'], true);

        return $body !== false ? $body : '';
    }

    /** @param array<string, list<string>> $headers */
    public function __construct(
        private string $method,
        private string $path,
        private string $queryString,
        private array $headers,
        private bool $isBase64Encoded,
        private string $body,
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function isBase64Encoded(): bool
    {
        return $this->isBase64Encoded;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
