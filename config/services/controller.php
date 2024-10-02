<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\Controller\FeedController;
use Webgriffe\SyliusClerkPlugin\Controller\SalesTrackingController;
use Webgriffe\SyliusClerkPlugin\Controller\TrackingCodeController;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider;

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
};
