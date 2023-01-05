<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationHttpEventContract;

final class APIGatewayVersionTwoEvent implements InvocationHttpEventContract
{
    public static function shouldHandle(array $data): bool
    {
        return isset($data['requestContext']) && floatval($data['version'] ?? 0.0) === 2.0;
    }

    public static function fromResponseData(array $data): self
    {
        return new self(
            method: strtoupper(strval($data['requestContext']['http']['method'] ?? 'GET')),
            path: strval($data['rawPath'] ?? '/'),
            queryString: self::resolveQueryStringFrom($data),
            headers: self::resolveHeadersFrom($data),
            isBase64Encoded: boolval($data['isBase64Encoded'] ?? false),
            body: self::resolveBodyFrom($data),
        );
    }

    /** @param array<string, scalar|mixed[][]> $data */
    private static function resolveQueryStringFrom(array $data): string
    {
        parse_str(strval($data['rawQueryString'] ?? ''), $decodedQueryParameters);

        return http_build_query($decodedQueryParameters);
    }

    /**
     * @param array<string, scalar|mixed[][]> $data
     * @return array<string, list<string>>
     */
    private static function resolveHeadersFrom(array $data): array
    {
        /** @var array<string, string> $headers */
        $headers = $data['headers'] ?? [];
        $headers = array_map(fn(string $value): array => [$value], $headers);
        $headers = array_change_key_case($headers, CASE_LOWER);

        /** @var list<string> $cookies */
        $cookies = $data['cookies'] ?? [];
        if ($cookies !== []) {
            $headers['cookie'] = [implode('; ', $cookies)];
        }

        $hasBody = ($data['body'] ?? '') !== '';

        if ($hasBody && !isset($headers['content-type'])) {
            $headers['content-type'] = ['application/x-www-form-urlencoded'];
        }

        if ($hasBody && !isset($headers['content-length'])) {
            $headers['content-length'] = [(string)strlen(self::resolveBodyFrom($data))];
        }

        return $headers;
    }

    /** @param array<string, scalar|mixed[][]> $data */
    private static function resolveBodyFrom(array $data): string
    {
        $body = strval($data['body'] ?? '');

        if (($data['isBase64Encoded'] ?? false) === true) {
            $body = base64_decode($body, true);
            $body = $body !== false ? $body : '';
        }

        return $body;
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

    public function getRequestMethod(): string
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
