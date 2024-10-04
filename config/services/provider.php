<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\PagesProvider;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\QueryBuilderResourceProvider;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk.provider.private_api_key', PrivateApiKeyProvider::class);

    $services->set('webgriffe_sylius_clerk.provider.public_api_key', PublicApiKeyProvider::class);

    $services->set('webgriffe_sylius_clerk_plugin.provider.products', QueryBuilderResourceProvider::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.query_builder.products'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.provider.categories', QueryBuilderResourceProvider::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.query_builder.categories'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.provider.orders', QueryBuilderResourceProvider::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.query_builder.orders'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.provider.customers', QueryBuilderResourceProvider::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.query_builder.customers'),
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.provider.pages', PagesProvider::class)
        ->args([
            '$urlGenerator' => service('router'),
        ])
    ;
};
