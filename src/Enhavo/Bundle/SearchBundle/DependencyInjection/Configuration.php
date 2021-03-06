<?php

namespace Enhavo\Bundle\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('enhavo_search');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

         $rootNode
            ->children()
                ->arrayNode('search')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('EnhavoSearchBundle:Search:render.html.twig')->end()
                        ->scalarNode('strategy')->defaultValue('reindex')->end()
                        ->scalarNode('search_engine')->defaultValue('enhavo_search_search_engine')->end()
                        ->scalarNode('index_engine')->defaultValue('enhavo_search_index_engine')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
