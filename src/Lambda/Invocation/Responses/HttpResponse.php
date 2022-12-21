<?php

namespace WPFortress\Runtime\Lambda\Invocation\Responses;

use WPFortress\Runtime\Contracts\InvocationResponseContract;

class HttpResponse implements InvocationResponseContract
{
    /** @param array<string, string|array<array-key, string>> $headers */
    public function __construct(
        private string $body,
        private array $headers = [],
        private int $status = 200,
        private float $formatVersion = 1.0,
    ) {
    }

    public function toApiGatewayFormat(): array
    {
        $data = [
            'isBase64Encoded' => false,
            'statusCode' => $this->status,
        ];

        $headersKey = $this->formatVersion === 1.0 ? 'multiValueHeaders' : 'headers';

        /** @var array<string, array<array-key, string>> $headers */
        $headers = [];
        foreach ($this->headers as $name => $values) {
            $headers[$this->capitalizeHeaderName($name)] = (array)$values;
        }

        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = ['text/html'];
        }

        if ($this->formatVersion === 2.0 && isset($headers['Set-Cookie'])) {
            $data['cookies'] = $headers['Set-Cookie'];
            unset($headers['Set-Cookie']);
        }

        if ($headersKey === 'headers') {
            foreach ($headers as $name => $values) {
                $headers[$name] = end($values);
            }
        }

        $data[$headersKey] = $headers;

        $data['body'] = $this->body;

        return $data;
    }

    private function capitalizeHeaderName(string $name): string
    {
        return str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
    }
}
