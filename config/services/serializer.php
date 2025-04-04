<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\CategoryNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\CustomerNormalizer as V2CustomerNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\OrderNormalizer as V2OrderNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\ProductNormalizer as V2ProductNormalizer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\ProductVariantNormalizer;
use Webgriffe\SyliusClerkPlugin\Normalizer\CustomerNormalizer;
use Webgriffe\SyliusClerkPlugin\Normalizer\OrderNormalizer;
use Webgriffe\SyliusClerkPlugin\Normalizer\ProductNormalizer;
use Webgriffe\SyliusClerkPlugin\Normalizer\TaxonNormalizer;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(ProductNormalizer::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'Service "%service_id%" is deprecated and will be removed in the next major version.')
        ->args([
            service('sylius.product_variant_resolver.default'),
            service('router'),
            service('liip_imagine.service.filter'),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(TaxonNormalizer::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'Service "%service_id%" is deprecated and will be removed in the next major version.')
        ->args([
            service('router'),
            service('sylius.repository.taxon'),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(OrderNormalizer::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'Service "%service_id%" is deprecated and will be removed in the next major version.')
        ->tag('serializer.normalizer', ['priority' => 120])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set(CustomerNormalizer::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'Service "%service_id%" is deprecated and will be removed in the next major version.')
        ->tag('serializer.normalizer', ['priority' => 120])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set('webgriffe_sylius_clerk_plugin.normalizer.product', V2ProductNormalizer::class)
        ->args([
            '$productVariantResolver' => service('sylius.product_variant_resolver.default'),
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
