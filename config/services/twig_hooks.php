<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webgriffe\SyliusClerkPlugin\TwigHooks\Component\ClerkIoSalesTrackingComponent;
use Webgriffe\SyliusClerkPlugin\TwigHooks\Component\ClerkIoTrackingComponent;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe_sylius_clerk_plugin.twig_hooks.component.clerk_io_tracking', ClerkIoTrackingComponent::class)
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service('webgriffe_sylius_clerk_plugin.provider.api_keys'),
        ])
        ->tag('sylius.twig_component', [
            'key' => 'webgriffe_sylius_clerk_plugin:clerk_io:tracking',
        ])
    ;

    $services->set('webgriffe_sylius_clerk_plugin.twig_hooks.component.clerk_io_sales_tracking', ClerkIoSalesTrackingComponent::class)
        ->tag('sylius.twig_component', [
            'key' => 'webgriffe_sylius_clerk_plugin:clerk_io:sales-tracking',
        ])
    ;
};
