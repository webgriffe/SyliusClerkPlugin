<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\CategoriesQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\CustomersQueryBuilder;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\ProductsQueryBuilder;
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
    ;

    $services->set(TaxonsQueryBuilderFactory::class)
        ->args([
            service('sylius.repository.taxon'),
        ])
    ;

    $services->set(OrdersQueryBuilderFactory::class)
        ->args([
            service('sylius.repository.order'),
        ])
    ;

    $services->set(CustomersQueryBuilderFactory::class)
        ->args([
            service('sylius.repository.customer'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.query_builder.products', ProductsQueryBuilder::class)
        ->args([
            service('sylius.repository.product'),
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
};
