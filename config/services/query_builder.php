<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\CategoriesQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\CustomersQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\OrdersQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\ProductsQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\ProductVariantsQueryBuilder;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\CustomersQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\OrdersQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\ProductsQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\TaxonsQueryBuilderFactory;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(ProductsQueryBuilderFactory::class)
        ->args([
            service('sylius.repository.product'),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set(TaxonsQueryBuilderFactory::class)
        ->args([
            service('sylius.repository.taxon'),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set(OrdersQueryBuilderFactory::class)
        ->args([
            service('sylius.repository.order'),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set(CustomersQueryBuilderFactory::class)
        ->args([
            service('sylius.repository.customer'),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.products', ProductsQueryBuilder::class)
        ->args([
            service('sylius.repository.product'),
            service('event_dispatcher'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.product_variants', ProductVariantsQueryBuilder::class)
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
