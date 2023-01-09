<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Unit\FastCGI;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WPFortress\Runtime\DependencyInjection\ContainerFactory;

final class ContainerFactoryTest extends TestCase
{
    /** @test */
    public function it_makes_container_from_given_config(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->makeFromConfig(__DIR__ . '/../../../config/services.yaml');

        self::assertInstanceOf(ContainerInterface::class, $container);
    }
}
