<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class ApplicationLoadBalancerEvent implements InvocationEventContract
{
    /**
     * @param array{
     *  requestContext: array{elb: array{targetGroupArn: string}},
     *  httpMethod: string,
     *  path: string,
     *  queryStringParameters: ?array<string, string>,
     *  multiValueQueryStringParameters: ?array<string, list<string>>,
     *  headers: ?array<string, string>,
     *  multiValueHeaders: ?array<string, list<string>>,
     *  isBase64Encoded: bool,
     *  body: string,
     * } $data
     */
    public static function fromResponseData(array $data): self
    {
        return new self(
            method: strtoupper($data['httpMethod']),
            path: $data['path'],
            queryString: self::resolveQueryStringFrom($data),
            usesMultiValueHeaders: isset($data['multiValueHeaders']),
            headers: self::resolveHeadersFrom($data),
            isBase64Encoded: $data['isBase64Encoded'],
            body: self::resolveBodyFrom($data),
        );
    }

    /**
     * @param array{
     *  queryStringParameters: ?array<string, string>,
     *  multiValueQueryStringParameters: ?array<string, list<string>>,
     * } $data
     */
    private static function resolveQueryStringFrom(array $data): string
    {
        if (isset($data['multiValueQueryStringParameters'])) {
            $queryStringParameters = $data['multiValueQueryStringParameters'];
        } else {
            $queryStringParameters = $data['queryStringParameters'] ?? [];
            $queryStringParameters = array_map(fn(string $value): array => [$value], $queryStringParameters);
        }

        $queryString = '';

        foreach ($queryStringParameters as $key => $values) {
            foreach ($values as $value) {
                $queryString .= $key . '=' . $value . '&';
            }
        }

        parse_str($queryString, $decodedQueryParameters);

        return http_build_query($decodedQueryParameters);
    }

    /**
     * @param array{
     *  headers: ?array<string, string>,
     *  multiValueHeaders: ?array<string, list<string>>,
     *  isBase64Encoded: bool,
     *  body: string,
     * } $data
     * @return array<string, list<string>>
     */
    private static function resolveHeadersFrom(array $data): array
    {
        if (isset($data['multiValueHeaders'])) {
            $headers = $data['multiValueHeaders'];
        } else {
            $headers = $data['headers'] ?? [];
            $headers = array_map(fn(string $value): array => [$value], $headers);
        }

        $headers = array_change_key_case($headers, CASE_LOWER);

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
        private bool $usesMultiValueHeaders,
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

    public function usesMultiValueHeaders(): bool
    {
        return $this->usesMultiValueHeaders;
    }

    /** @return array<string, list<string>> */
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
