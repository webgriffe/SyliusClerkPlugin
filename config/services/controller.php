<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Controller\FeedController;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.controller.feed', FeedController::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.repository.locale'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.products'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.categories'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.orders'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.customers'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.pages'),
            service('webgriffe_sylius_clerk_plugin.validator.request'),
        ])
        ->call('setContainer', [service('service_container')])
        ->tag('controller.service_arguments')
        ->tag('container.service_subscriber')
    ;

    $services->alias(FeedController::class, 'webgriffe_sylius_clerk_plugin.controller.feed');
};
