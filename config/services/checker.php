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
    ;
};
