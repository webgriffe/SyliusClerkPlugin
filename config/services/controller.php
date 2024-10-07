<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Controller\FeedController;
use Webgriffe\SyliusClerkPlugin\Controller\SalesTrackingController;
use Webgriffe\SyliusClerkPlugin\Controller\TrackingCodeController;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Controller\FeedController as V2FeedController;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(FeedController::class)
        ->public()
        ->args([
            service(FeedGenerator::class),
            service('sylius.repository.channel'),
            service('webgriffe_sylius_clerk.provider.private_api_key'),
            service('webgriffe_sylius_clerk.checker.channel_enabled'),
        ])
        ->tag('controller.service_arguments')
        ->call('setContainer', [service('service_container')])
    ;

    $services->set(TrackingCodeController::class)
        ->public()
        ->args([
            service('sylius.context.channel'),
            service('webgriffe_sylius_clerk.provider.public_api_key'),
            service('webgriffe_sylius_clerk.checker.channel_enabled'),
        ])
        ->tag('controller.service_arguments')
        ->call('setContainer', [service('service_container')])
    ;

    $services->set(SalesTrackingController::class)
        ->public()
        ->args([
            service('sylius.repository.order'),
            service('serializer'),
            service('webgriffe_sylius_clerk.checker.channel_enabled'),
        ])
        ->tag('controller.service_arguments')
        ->call('setContainer', [service('service_container')])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.controller.feed', V2FeedController::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.repository.locale'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.products'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.categories'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.orders'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.customers'),
            service('webgriffe_sylius_clerk_plugin.feed_generator.pages'),
            service('webgriffe_sylius_clerk_plugin.validator.request'),
            false,
        ])
        ->call('setContainer', [service('service_container')])
        ->tag('controller.service_arguments')
        ->tag('container.service_subscriber')
    ;
    $services->alias(V2FeedController::class, 'webgriffe_sylius_clerk_plugin.controller.feed');
};
