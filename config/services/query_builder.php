<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

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
};
