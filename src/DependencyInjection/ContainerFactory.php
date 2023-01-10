<?php

declare(strict_types=1);

namespace WPFortress\Runtime\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ContainerFactory
{
    public static function makeFromConfig(string $path): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator());
        $loader->load($path);

        $containerBuilder->compile(true);

        return $containerBuilder;
    }
}
