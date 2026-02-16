<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\CategoryNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\CustomerNormalizer as V2CustomerNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\OrderNormalizer as V2OrderNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\ProductNormalizer as V2ProductNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\ProductVariantNormalizer;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.normalizer.product', V2ProductNormalizer::class)
        ->args([
            '$productVariantResolver' => service('sylius.resolver.product_variant'),
            '$eventDispatcher' => service('event_dispatcher'),
            '$productVariantPricesCalculator' => service('sylius.calculator.product_variant_price'),
            '$urlGenerator' => service('router'),
            '$cacheManager' => service('liip_imagine.cache.manager'),
            '$fallbackLocale' => param('kernel.default_locale'),
        ])
        ->tag('serializer.normalizer', ['priority' => 100])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.normalizer.product_variant', ProductVariantNormalizer::class)
        ->args([
            '$eventDispatcher' => service('event_dispatcher'),
            '$productVariantPricesCalculator' => service('sylius.calculator.product_variant_price'),
            '$urlGenerator' => service('router'),
            '$cacheManager' => service('liip_imagine.cache.manager'),
            '$fallbackLocale' => param('kernel.default_locale'),
        ])
        ->tag('serializer.normalizer', ['priority' => 100])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.normalizer.category', CategoryNormalizer::class)
        ->args([
            '$eventDispatcher' => service('event_dispatcher'),
            '$urlGenerator' => service('router'),
        ])
        ->tag('serializer.normalizer', ['priority' => 100])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.normalizer.customer', V2CustomerNormalizer::class)
        ->args([
            '$eventDispatcher' => service('event_dispatcher'),
        ])
        ->tag('serializer.normalizer', ['priority' => 100])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.normalizer.order', V2OrderNormalizer::class)
        ->args([
            '$eventDispatcher' => service('event_dispatcher'),
        ])
        ->tag('serializer.normalizer', ['priority' => 100])
    ;
};
