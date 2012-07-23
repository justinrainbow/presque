<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This class contains the configuration information for the Presque
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return NodeInterface
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('presque');

        $rootNode
            ->children()
                ->arrayNode('connection')
                    ->addDefaultsIfNotSet()
                    ->prototype('array')
                        ->scalarNode('dsn')
                            ->defaultValue('tcp://localhost:6379')
                            // ->beforeNormalization()
                            //     ->ifTrue(function($v) { return null !== $v; })
                            //     ->then(function($v) {
                            //         throw new \RuntimeException($message);
                            //     })
                            // ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    protected function addQueuesConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->fixXmlConfig('queue')
                ->children()
                    ->arrayNode('queues')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('max_failures')->defaultValue(3)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}