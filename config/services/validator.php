<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Validator\RequestValidator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.validator.request', RequestValidator::class)
        ->args([
            service('webgriffe_sylius_clerk_plugin.provider.api_keys'),
            service('webgriffe_sylius_clerk_plugin.client'),
        ])
    ;
};
