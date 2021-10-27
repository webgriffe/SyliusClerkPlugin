<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
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
                            ->scalarNode('channel_code')->isRequired()->end()
                            ->scalarNode('public_api_key')->isRequired()->end()
                            ->scalarNode('private_api_key')->isRequired()->end()
                        ->end()
                    ->end()
                ->end() // private_api_keys
            ->end()
        ;

        return $treeBuilder;
    }
}
