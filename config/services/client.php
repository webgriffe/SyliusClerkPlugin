<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use GuzzleHttp\Client as GuzzleClient;
use Webgriffe\SyliusClerkPlugin\Client\Client;
use Webgriffe\SyliusClerkPlugin\Client\ClientInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.http_client', GuzzleClient::class);

    $services->set('webgriffe_sylius_clerk_plugin.client', Client::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.http_client'),
            service('webgriffe_sylius_clerk_plugin.logger'),
        ])
    ;

    $services->alias(ClientInterface::class, 'webgriffe_sylius_clerk_plugin.client');
};
