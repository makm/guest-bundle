<?php

namespace Makm\GuestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('makm_guest');

        $rootNode
            ->children()
            ->scalarNode('override_anonymous_provider')->defaultFalse()->end()
            ->end()
            ->children()
            ->arrayNode('remember_cookie')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('ttl')->defaultValue('0')->end()
            ->scalarNode('name')->defaultValue('_guest_data')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
