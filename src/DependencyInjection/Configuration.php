<?php

namespace JvH\CadeauBonnenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jvh_cadeaubonnen');
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('grootboek_cadeabonnen')->isRequired()->end()
            ->scalarNode('verkoop_grootboek_cadeabonnen_nl')->isRequired()->end()
            ->scalarNode('verkoop_grootboek_cadeabonnen_eu')->isRequired()->end()
            ->scalarNode('verkoop_grootboek_cadeabonnen_wereld')->isRequired()->end()
            ->end();
        return $treeBuilder;
    }


}