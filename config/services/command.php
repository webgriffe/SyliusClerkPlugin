<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Command\FeedGeneratorCommand;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk.command.generate_feed', FeedGeneratorCommand::class)
        ->args([
            '$feedGenerator' => service(FeedGenerator::class),
            '$channelRepository' => service('sylius.repository.channel'),
            '$router' => service('router'),
            '$logger' => service('monolog.logger'),
        ])
        ->tag('console.command')
    ;
};
