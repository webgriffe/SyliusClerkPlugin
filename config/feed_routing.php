<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Webgriffe\SyliusClerkPlugin\Controller\FeedController;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;

return static function (RoutingConfigurator $routes): void {
    $routes->add('webgriffe_sylius_clerk_feed', '/clerk/feed/{channelId}')
        ->controller([FeedController::class, 'feedAction'])
    ;

    $routes->add('webgriffe_sylius_clerk_plugin_feed', '/clerk/feed/{channelCode}/{localeCode}/{resourceValue}')
        ->controller(['webgriffe_sylius_clerk_plugin.controller.feed', '__invoke'])
        ->methods(['GET'])
        ->requirements([
            'localeCode' => '^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$',
            'resourceValue' => class_exists(EnumRequirement::class) ? new EnumRequirement(Resource::class) : 'products|categories|orders|customers|pages',
        ])
    ;
};
