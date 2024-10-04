<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\ProductNormalizer as V2ProductNormalizer;
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
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(TaxonNormalizer::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'Service "%service_id%" is deprecated and will be removed in the next major version.')
        ->args([
            service('router'),
            service('sylius.repository.taxon'),
        ])
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(OrderNormalizer::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'Service "%service_id%" is deprecated and will be removed in the next major version.')
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(CustomerNormalizer::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'Service "%service_id%" is deprecated and will be removed in the next major version.')
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.normalizer.product', V2ProductNormalizer::class)
        ->args([
            '$productVariantResolver' => service(ProductVariantResolverInterface::class),
            '$eventDispatcher' => service('event_dispatcher'),
            '$productVariantPricesCalculator' => service('sylius.calculator.product_variant_price'),
            '$urlGenerator' => service('router'),
            '$cacheManager' => service('liip_imagine.cache.manager'),
        ])
        ->tag('serializer.normalizer', ['priority' => 100])
    ;
};
