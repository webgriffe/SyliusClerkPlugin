<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Command\FeedGeneratorCommand;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk.command.generate_feed', FeedGeneratorCommand::class)
        ->args([
            service(FeedGenerator::class),
            service('sylius.repository.channel'),
            service('router'),
            service('monolog.logger'),
            param('webgriffe_sylius_clerk.storage_feed_path'),
        ])
        ->tag('console.command')
    ;
};
