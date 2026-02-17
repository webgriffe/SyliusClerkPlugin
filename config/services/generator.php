<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Generator\ResourceFeedGenerator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.products', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.products'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            Resource::PRODUCTS,
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.categories', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.categories'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            Resource::CATEGORIES,
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.orders', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.orders'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            Resource::ORDERS,
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.customers', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.customers'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            Resource::CUSTOMERS,
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.feed_generator.pages', ResourceFeedGenerator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.pages'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.logger'),
            Resource::PAGES,
        ])
    ;
};
