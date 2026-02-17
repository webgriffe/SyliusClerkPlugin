<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_shop.base#javascripts' => [
                'clerk_io' => [
                    'component' => 'webgriffe_sylius_clerk_plugin:clerk_io:tracking',
                    'props' => [
                        'template' => '@WebgriffeSyliusClerkPlugin/shop/shared/layout/base/clerk_io.html.twig',
                    ],
                    'priority' => 100,
                ],
            ],
            'sylius_shop.order.thank_you' => [
                'clerk_io' => [
                    'component' => 'webgriffe_sylius_clerk_plugin:clerk_io:sales-tracking',
                    'props' => [
                        'order' => '@=_context.order',
                        'template' => '@WebgriffeSyliusClerkPlugin/shop/order/thank_you/clerk_io.html.twig',
                    ],
                    'priority' => 100,
                ],
            ],
        ],
    ]);
};
