<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use GuzzleHttp\Client;
use Webgriffe\SyliusClerkPlugin\Client\ClientInterface;
use Webgriffe\SyliusClerkPlugin\Client\V2Client;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.http_client', Client::class);

    $services->set('webgriffe_sylius_clerk_plugin.v2client', V2Client::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.http_client'),
            service('webgriffe_sylius_clerk_plugin.logger'),
        ]);

    $services->alias('webgriffe_sylius_clerk_plugin.client', 'webgriffe_sylius_clerk_plugin.v2client');
    $services->alias(ClientInterface::class, 'webgriffe_sylius_clerk_plugin.client');
};
