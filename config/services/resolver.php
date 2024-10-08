<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\QueryBuilder\OrdersQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\Resolver\OrderResolver;
use Webgriffe\SyliusClerkPlugin\Resolver\PageResolver;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(PageResolver::class);

    $services->set(OrderResolver::class)
        ->args([
            service(OrdersQueryBuilderFactory::class),
        ])
    ;
};
