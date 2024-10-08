<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyChecker;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk.checker.channel_enabled', ChannelApiKeyChecker::class)
        ->args([
            service('webgriffe_sylius_clerk.provider.public_api_key'),
            service('webgriffe_sylius_clerk.provider.private_api_key'),
        ])
        ->deprecate('webgriffe/sylius-clerk-plugin', '3.0', 'The "%service_id%" service is deprecated and will be removed in 4.0.')
    ;
};
