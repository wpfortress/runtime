<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Console;

use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\Console\Application;

final class ApplicationTest extends TestCase
{
    /** @test */
    public function it_configures_console_application(): void
    {
        $application = new Application([]);

        self::assertSame(Application::NAME, $application->getName());
    }
}
