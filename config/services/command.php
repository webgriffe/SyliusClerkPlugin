<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Command\FeedGeneratorCommand;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Command\V2FeedGeneratorCommand;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.command.feed_generator', V2FeedGeneratorCommand::class)
        ->args([
            '$channelRepository' => service('sylius.repository.channel'),
            '$localeRepository' => service('sylius.repository.locale'),
            '$productsFeedGenerator' => service('webgriffe_sylius_clerk_plugin.feed_generator.products'),
            '$categoriesFeedGenerator' => service('webgriffe_sylius_clerk_plugin.feed_generator.categories'),
            '$customersFeedGenerator' => service('webgriffe_sylius_clerk_plugin.feed_generator.customers'),
            '$ordersFeedGenerator' => service('webgriffe_sylius_clerk_plugin.feed_generator.orders'),
            '$pagesFeedGenerator' => service('webgriffe_sylius_clerk_plugin.feed_generator.pages'),
            '$filesystem' => service('filesystem'),
        ])
        ->tag('console.command')
    ;
};
