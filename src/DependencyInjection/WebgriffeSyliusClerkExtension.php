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
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.php');

        $apiKeysProviderServiceDefinition = $container->getDefinition('webgriffe_sylius_clerk_plugin.provider.api_keys');
        $apiKeysProviderServiceDefinition->setArgument('$storesConfiguration', $config['stores']);

        $feedController = $container->getDefinition('webgriffe_sylius_clerk_plugin.controller.feed');
        $feedController->setArgument('$isTokenAuthenticationEnabled', $config['token_authentication_enabled']);

        $newGenerateFeedCommand = $container->getDefinition('webgriffe_sylius_clerk_plugin.command.feed_generator');
        $newGenerateFeedCommand->setArgument('$feedsStorageDirectory', $config['storage_feed_path']);

        $productNormalizer = $container->getDefinition('webgriffe_sylius_clerk_plugin.normalizer.product');
        $productNormalizer->setArgument('$imageType', $config['image_type']);
        $productNormalizer->setArgument('$imageFilterToApply', $config['image_filter_to_apply']);
        if ($container->hasDefinition(ProductVariantResolverInterface::class)) {
            $productNormalizer->setArgument('$productVariantResolver', $container->getDefinition(ProductVariantResolverInterface::class));
        }
        $productVariantNormalizer = $container->getDefinition('webgriffe_sylius_clerk_plugin.normalizer.product_variant');
        $productVariantNormalizer->setArgument('$imageType', $config['image_type']);
        $productVariantNormalizer->setArgument('$imageFilterToApply', $config['image_filter_to_apply']);

        $pagesProvider = $container->getDefinition('webgriffe_sylius_clerk_plugin.provider.pages');
        $pagesProvider->setArgument('$pages', $config['pages']);

        /** @var bool $useProductVariants */
        $useProductVariants = $config['use_product_variants'];
        $productsProvider = $container->getDefinition('webgriffe_sylius_clerk_plugin.provider.products');
        $productsQueryBuilder = $container->getDefinition('webgriffe_sylius_clerk_plugin.query_builder.products');
        $productVariantsQueryBuilder = $container->getDefinition('webgriffe_sylius_clerk_plugin.query_builder.product_variants');
        $productsProvider->setArgument('$queryBuilder', $useProductVariants ? $productVariantsQueryBuilder : $productsQueryBuilder);
        $orderNormalizer = $container->getDefinition('webgriffe_sylius_clerk_plugin.normalizer.order');
        $orderNormalizer->setArgument('$useProductVariants', $useProductVariants);
    }

    #[\Override]
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
