<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\QueryBuilder\OrdersQueryBuilderFactory;
use Webgriffe\SyliusClerkPlugin\Resolver\OrderResolver;
use Webgriffe\SyliusClerkPlugin\Resolver\PageResolver;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(PageResolver::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set(OrderResolver::class)
        ->args([
            service(OrdersQueryBuilderFactory::class),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;
};
