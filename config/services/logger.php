<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->alias('webgriffe_sylius_clerk_plugin.logger', 'monolog.logger.webgriffe_sylius_clerk_plugin');
};
