<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider;

final class WebgriffeSyliusClerkExtension extends Extension
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $definition = $container->getDefinition(PrivateApiKeyProvider::class);
        $definition->replaceArgument(0, $config['stores']);
        $definition = $container->getDefinition(PublicApiKeyProvider::class);
        $definition->replaceArgument(0, $config['stores']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
