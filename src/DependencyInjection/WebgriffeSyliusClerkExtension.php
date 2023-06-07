<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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

        $definition = $container->getDefinition('webgriffe_sylius_clerk.provider.private_api_key');
        $definition->replaceArgument(0, $config['stores']);
        $definition = $container->getDefinition('webgriffe_sylius_clerk.provider.public_api_key');
        $definition->replaceArgument(0, $config['stores']);

        $container->setParameter('webgriffe_sylius_clerk.storage_feed_path', (string) $config['storage_feed_path']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
