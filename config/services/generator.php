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
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.products', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.products'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            'products',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.categories', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.categories'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            'categories',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.orders', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.orders'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            'orders',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.customers', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.customers'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            'customers',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.pages', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.pages'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            'pages',
        ])
    ;
};
