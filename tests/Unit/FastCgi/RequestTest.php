<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\FastCgi;

use hollodotme\FastCGI\Requests\AbstractRequest;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\FastCgi\Request;

final class RequestTest extends TestCase
{
    /** @test */
    public function it_forms_correct_fastcgi_request(): void
    {
        $request = new Request(
            method: $expectedMethod = 'GET',
            scriptFilename: '',
            content: ''
        );

        self::assertInstanceOf(AbstractRequest::class, $request);
        self::assertSame($expectedMethod, $request->getRequestMethod());
        self::assertSame('wpfortress', $request->getServerSoftware());
    }
}
