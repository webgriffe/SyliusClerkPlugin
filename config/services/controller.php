<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Controller\SalesTrackingController;
use Webgriffe\SyliusClerkPlugin\Controller\TrackingCodeController;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Controller\FeedController;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(TrackingCodeController::class)
        ->public()
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service('webgriffe_sylius_clerk_plugin.provider.api_keys'),
        ])
        ->tag('controller.service_arguments')
        ->call('setContainer', [service('service_container')])
    ;

    $services->set(SalesTrackingController::class)
        ->public()
        ->args([
            service('sylius.repository.order'),
            service('serializer'),
            service('webgriffe_sylius_clerk_plugin.provider.api_keys'),
        ])
        ->tag('controller.service_arguments')
        ->call('setContainer', [service('service_container')])
    ;

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
