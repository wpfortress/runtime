<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Lambda\Invocation\Events;

use WPFortress\Runtime\Contracts\InvocationEventContract;

final class HttpEvent extends Event implements InvocationEventContract
{
    private ?string $queryString = null;

    /** @var null|array<string, array<array-key, string>> */
    private ?array $headers = null;

    private ?string $body = null;

    public function getProtocol(): string
    {
        return $this->data['requestContext']['protocol']
            ?? $this->data['requestContext']['http']['protocol']
            ?? 'HTTP/1.1';
    }

    public function getMethod(): string
    {
        return strtoupper($this->data['httpMethod'] ?? $this->data['requestContext']['http']['method'] ?? 'GET');
    }

    public function getPath(): string
    {
        return $this->data['path'] ?? $this->data['rawPath'] ?? '/';
    }

    public function getUri(): string
    {
        $path = $this->getPath();
        $queryString = $this->getQueryString();

        if ($queryString === '') {
            return $path;
        }

        return $path . '?' . $queryString;
    }

    public function getQueryString(): string
    {
        if ($this->queryString !== null) {
            return $this->queryString;
        }

        if (isset($this->data['rawQueryString'])) {
            $queryString = $this->data['rawQueryString'];

            if ($queryString === '') {
                return $this->queryString = '';
            }

            parse_str($queryString, $decodedQueryParameters);

            return $this->queryString = http_build_query($decodedQueryParameters);
        }

        /** @var array<string, string|array<array-key, string>> $queryParameters */
        $queryParameters = $this->data['multiValueQueryStringParameters']
            ?? $this->data['queryStringParameters']
            ?? [];

        if ($queryParameters === []) {
            return $this->queryString = '';
        }

        $queryString = '';

        foreach ($queryParameters as $key => $values) {
            $values = (array)$values;

            $queryString .= array_reduce(
                $values,
                fn(string $carry, string $value): string => $carry . $key . '=' . urlencode($value) . '&',
                ''
            );
        }

        parse_str($queryString, $decodedQueryParameters);

        return $this->queryString = http_build_query($decodedQueryParameters);
    }

    /** @return array<string, array<array-key, string>> */
    public function getHeaders(): array
    {
        if ($this->headers !== null) {
            return $this->headers;
        }

        $headers = $this->data['multiValueHeaders'] ?? $this->data['headers'] ?? [];
        $headers = array_map(
            fn(string|array $value): array => is_string($value) ? [$value] : $value,
            $headers
        );
        $headers = array_change_key_case($headers);

        $hasBody = ($this->data['body'] ?? '') !== '';

        if ($hasBody && !isset($headers['content-type'])) {
            $headers['content-type'] = ['application/x-www-form-urlencoded'];
        }

        if ($hasBody && !isset($headers['content-length'])) {
            $headers['content-length'] = [strlen($this->getBody())];
        }

        if (isset($this->data['cookies'])) {
            $headers['cookie'] = [implode('; ', $this->data['cookies'])];
        }

        return $this->headers = $headers;
    }

    public function getContentType(): ?string
    {
        return $this->getHeaders()['content-type'][0] ?? null;
    }

    public function getServerPort(): int
    {
        return (int)($this->getHeaders()['x-forwarded-port'][0] ?? 80);
    }

    public function getServerName(): string
    {
        return $this->getHeaders()['host'][0] ?? 'localhost';
    }

    public function getBody(): string
    {
        if ($this->body !== null) {
            return $this->body;
        }

        $body = $this->data['body'] ?? '';

        // @phpstan-ignore-next-line
        if ($this->data['isBase64Encoded'] ?? false) {
            $body = base64_decode($body, true);

            if ($body === false) {
                $body = '';
            }
        }

        return $this->body = $body;
    }

    public function getSourceIp(): string
    {
        return $this->data['requestContext']['http']['sourceIp']
            ?? $this->data['requestContext']['identity']['sourceIp']
            ?? '127.0.0.1';
    }
}
