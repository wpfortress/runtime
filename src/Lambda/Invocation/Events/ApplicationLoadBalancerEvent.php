<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\LambdaInvocationHttpEventContract;

final class ApplicationLoadBalancerEvent implements LambdaInvocationHttpEventContract
{
    public static function shouldHandle(array $data): bool
    {
        return isset($data['requestContext']['elb']);
    }

    public static function fromResponseData(array $data): self
    {
        return new self(
            method: strtoupper(strval($data['httpMethod'])),
            path: strval($data['path']),
            queryString: self::resolveQueryStringFrom($data),
            usesMultiValueHeaders: isset($data['multiValueHeaders']),
            headers: self::resolveHeadersFrom($data),
            isBase64Encoded: boolval($data['isBase64Encoded']),
            body: self::resolveBodyFrom($data),
        );
    }

    /** @param array<string, scalar|mixed[][]> $data */
    private static function resolveQueryStringFrom(array $data): string
    {
        if (isset($data['multiValueQueryStringParameters'])) {
            /** @var array<string, list<string>> $queryStringParameters */
            $queryStringParameters = $data['multiValueQueryStringParameters'];
        } else {
            /** @var array<string, string> $queryStringParameters */
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
     * @param array<string, scalar|mixed[][]> $data
     * @return array<string, list<string>>
     */
    private static function resolveHeadersFrom(array $data): array
    {
        if (isset($data['multiValueHeaders'])) {
            /** @var array<string, list<string>> $headers */
            $headers = $data['multiValueHeaders'];
        } else {
            /** @var array<string, string> $headers */
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
        private bool $usesMultiValueHeaders,
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

    public function usesMultiValueHeaders(): bool
    {
        return $this->usesMultiValueHeaders;
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
