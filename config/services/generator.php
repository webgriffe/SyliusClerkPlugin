<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\QueryBuilder\CustomersQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\ProductsQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\TaxonsQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\Resolver\OrderResolver;
use Webgriffe\SyliusClerkPlugin\Resolver\PageResolver;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(FeedGenerator::class)
        ->args([
            service(ProductsQueryBuilderFactory::class),
            service(TaxonsQueryBuilderFactory::class),
            service(OrderResolver::class),
            service(CustomersQueryBuilderFactory::class),
            service(PageResolver::class),
            service('serializer'),
        ])
    ;
};
