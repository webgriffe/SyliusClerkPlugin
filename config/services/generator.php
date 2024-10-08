<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Generator\ResourceFeedGenerator;
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

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.products', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.products'),
            service('serializer'),
            'products',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.categories', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.categories'),
            service('serializer'),
            'categories',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.orders', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.orders'),
            service('serializer'),
            'orders',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.customers', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.customers'),
            service('serializer'),
            'customers',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.pages', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.pages'),
            service('serializer'),
            'pages',
        ])
    ;
};
