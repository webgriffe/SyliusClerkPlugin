<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\CategoriesQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\CustomersQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\OrdersQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\ProductsQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\ProductVariantsQueryBuilder;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.products', ProductsQueryBuilder::class)
        ->public()
        ->args([
            service('sylius.repository.product'),
            service('event_dispatcher'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.product_variants', ProductVariantsQueryBuilder::class)
        ->public()
        ->args([
            service('sylius.repository.product_variant'),
            service('event_dispatcher'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.categories', CategoriesQueryBuilder::class)
        ->args([
            service('sylius.repository.taxon'),
            service('event_dispatcher'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.customers', CustomersQueryBuilder::class)
        ->args([
            service('sylius.repository.customer'),
            service('event_dispatcher'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.orders', OrdersQueryBuilder::class)
        ->args([
            service('sylius.repository.order'),
            service('event_dispatcher'),
        ])
    ;
};
