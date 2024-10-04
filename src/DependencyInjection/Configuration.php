<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UndefinedMethod
     * @psalm-suppress MixedMethodCall
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('webgriffe_sylius_clerk');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('stores')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('channel_code')
                                ->isRequired()
                            ->end()
                            ->scalarNode('public_api_key')
                                ->isRequired()
                            ->end()
                            ->scalarNode('private_api_key')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $rootNode
            ->children()
                ->scalarNode('storage_feed_path')
                    ->defaultValue('%kernel.project_dir%/public/feed/clerk.io')
                ->end()
            ->end()
        ;

        $rootNode
            ->children()
                ->scalarNode('image_type')
                    ->info('The type of the image to use for the product. If none is specified or the type does not exists on current product then the first image will be used.')
                    ->defaultValue('main')
                ->end()
            ->end()
        ;

        $rootNode
            ->children()
                ->scalarNode('image_filter_to_apply')
                    ->info('Liip filter to apply to the image. If none is specified then the original image will be used.')
                    ->defaultValue('sylius_medium')
                ->end()
            ->end()
        ;

        $rootNode
            ->children()
                ->arrayNode('pages')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('id')
                                ->isRequired()
                            ->end()
                            ->scalarNode('type')
                                ->isRequired()
                            ->end()
                            ->scalarNode('routeName')
                                ->isRequired()
                            ->end()
                            ->arrayNode('routeParameters')
                                ->isRequired()
                            ->end()
                            ->scalarNode('title')
                                ->isRequired()
                            ->end()
                            ->scalarNode('text')
                                ->isRequired()
                            ->end()
                            ->scalarNode('image')
                                ->defaultNull()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
