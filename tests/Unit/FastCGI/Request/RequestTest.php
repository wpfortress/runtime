<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\FastCGI\Request;

use hollodotme\FastCGI\Requests\AbstractRequest;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\FastCGI\Request\Request;

final class RequestTest extends TestCase
{
    /** @test */
    public function it_forms_correct_request(): void
    {
        $request = new Request(scriptFilename: 'foo.php', content: 'foo');

        self::assertInstanceOf(AbstractRequest::class, $request);
    }
}
