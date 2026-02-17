<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Webgriffe\SyliusClerkPlugin\Controller\SalesTrackingController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('webgriffe_sylius_clerk_sales_tracking', '/clerk-sales-tracking/{orderId}')
        ->controller([SalesTrackingController::class, 'indexAction'])
    ;
};
