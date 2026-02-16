<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Api\ClerkFeedContext;
use Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Setup\ProductContext;
use Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Shop\ClerkSalesTrackingContext;
use Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Shop\ClerkTrackingCodeContext;
use Tests\Webgriffe\SyliusClerkPlugin\Behat\Page\Shop\ThankYouPage;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public();

    $services->set(ClerkFeedContext::class)
        ->args([
            service('test.client'),
        ])
    ;

    $services->set(ProductContext::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.resolver.product_variant'),
        ])
    ;

    $services->set(ClerkTrackingCodeContext::class)
        ->args([
            service('sylius.behat.page.shop.home'),
        ])
    ;

    $services->set(ClerkSalesTrackingContext::class)
        ->args([
            service(ThankYouPage::class),
        ])
    ;

    $services->set(ThankYouPage::class)
        ->parent('sylius.behat.page.shop.order.thank_you')
        ->private()
    ;
};
