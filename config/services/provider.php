<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\PagesProvider;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\QueryBuilderResourceProvider;
use Webgriffe\SyliusClerkPlugin\Provider\ApiKeysProvider;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk.provider.private_api_key', PrivateApiKeyProvider::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set('webgriffe_sylius_clerk.provider.public_api_key', PublicApiKeyProvider::class)
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;

    $services->set('webgriffe_sylius_clerk_plugin.provider.products', QueryBuilderResourceProvider::class);

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

    $services->set('webgriffe_sylius_clerk_plugin.provider.api_keys', ApiKeysProvider::class);
};
