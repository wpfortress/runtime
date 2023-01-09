<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\Lambda\Invocation\Responses;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Lambda\Invocation\Responses\StaticFileResponse;

final class StaticFileResponseTest extends TestCase
{
    /** @test */
    public function it_forms_correct_static_file_response(): void
    {
        $filename = stream_get_meta_data(tmpfile())['uri'];
        file_put_contents($filename, 'foo');

        $response = StaticFileResponse::fromFilename($filename);

        self::assertSame('foo', $response->getBody());
        self::assertSame(['Content-Type' => ['text/plain']], $response->getHeaders());
    }
}
