<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk.provider.private_api_key', PrivateApiKeyProvider::class);

    $services->set('webgriffe_sylius_clerk.provider.public_api_key', PublicApiKeyProvider::class);
};
