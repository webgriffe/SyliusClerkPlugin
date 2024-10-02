<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Webgriffe\SyliusClerkPlugin\Controller\FeedController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('webgriffe_sylius_clerk_feed', '/clerk/feed/{channelId}')
        ->controller([FeedController::class, 'feedAction'])
    ;
};
