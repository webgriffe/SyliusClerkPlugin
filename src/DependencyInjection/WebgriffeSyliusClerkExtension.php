<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DependencyInjection;

use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class WebgriffeSyliusClerkExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.php');

        $privateApiKeyProviderServiceDefinition = $container->getDefinition('webgriffe_sylius_clerk.provider.private_api_key');
        $privateApiKeyProviderServiceDefinition->setArgument('$clerkStores', $config['stores']);
        $publicApiKeyProviderServiceDefinition = $container->getDefinition('webgriffe_sylius_clerk.provider.public_api_key');
        $publicApiKeyProviderServiceDefinition->setArgument('$clerkStores', $config['stores']);
        $apiKeysProviderServiceDefinition = $container->getDefinition('webgriffe_sylius_clerk_plugin.provider.api_keys');
        $apiKeysProviderServiceDefinition->setArgument('$storesConfiguration', $config['stores']);

        $feedController = $container->getDefinition('webgriffe_sylius_clerk_plugin.controller.feed');
        $feedController->setArgument('$isTokenAuthenticationEnabled', $config['token_authentication_enabled']);

        $generateFeedCommand = $container->getDefinition('webgriffe_sylius_clerk.command.generate_feed');
        $generateFeedCommand->setArgument('$storagePath', $config['storage_feed_path']);

        $newGenerateFeedCommand = $container->getDefinition('webgriffe_sylius_clerk_plugin.command.feed_generator');
        $newGenerateFeedCommand->setArgument('$feedsStorageDirectory', $config['storage_feed_path']);

        $productNormalizer = $container->getDefinition('webgriffe_sylius_clerk_plugin.normalizer.product');
        $productNormalizer->setArgument('$imageType', $config['image_type']);
        $productNormalizer->setArgument('$imageFilterToApply', $config['image_filter_to_apply']);
        if ($container->hasDefinition(ProductVariantResolverInterface::class)) {
            $productNormalizer->setArgument('$productVariantResolver', $container->getDefinition(ProductVariantResolverInterface::class));
        }

        $pagesProvider = $container->getDefinition('webgriffe_sylius_clerk_plugin.provider.pages');
        $pagesProvider->setArgument('$pages', $config['pages']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
