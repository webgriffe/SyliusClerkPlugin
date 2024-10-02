<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Normalizer\CustomerNormalizer;
use Webgriffe\SyliusClerkPlugin\Normalizer\OrderNormalizer;
use Webgriffe\SyliusClerkPlugin\Normalizer\ProductNormalizer;
use Webgriffe\SyliusClerkPlugin\Normalizer\TaxonNormalizer;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(ProductNormalizer::class)
        ->args([
            service('sylius.product_variant_resolver.default'),
            service('router'),
            service('liip_imagine.service.filter'),
        ])
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(TaxonNormalizer::class)
        ->args([
            service('router'),
            service('sylius.repository.taxon'),
        ])
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(OrderNormalizer::class)
        ->tag('serializer.normalizer', ['priority' => 120])
    ;

    $services->set(CustomerNormalizer::class)
        ->tag('serializer.normalizer', ['priority' => 120])
    ;
};
