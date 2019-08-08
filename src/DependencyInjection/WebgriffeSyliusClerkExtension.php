<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider;

final class WebgriffeSyliusClerkExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $definition = $container->getDefinition(PrivateApiKeyProvider::class);
        $definition->replaceArgument(0, $config['stores']);
    }
}
