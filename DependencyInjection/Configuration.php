<?php

namespace Socloz\MonitoringBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('socloz_monitoring');

        $rootNode
            ->children()
                ->arrayNode('exceptions')
                    ->children()
                        ->booleanNode('enable')->defaultValue(true)->end()
                        ->variableNode('ignore')->defaultValue(array('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', 'Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException'))->end()
                    ->end()
                ->end()
                ->arrayNode('profiler')
                    ->children()
                        ->booleanNode('enable')->defaultValue(true)->end()
                        ->scalarNode('sampling')->defaultValue(100)->end()
                        ->booleanNode('request')->defaultValue(false)->end()
                        ->booleanNode('mongodb')->defaultValue(false)->end()
                        ->booleanNode('memory')->defaultValue(false)->end()
                        ->booleanNode('sphinx')->defaultValue(false)->end()
                        ->booleanNode('redis')->defaultValue(false)->end()
                        ->booleanNode('curl')->defaultValue(false)->end()
                        ->variableNode('calls')->defaultValue(array())->end()
                    ->end()
                ->end()
                ->arrayNode('mailer')
                    ->children()
                        ->booleanNode('enable')->defaultValue(true)->end()
                        ->scalarNode('from')->end()
                        ->scalarNode('to')->end()
                    ->end()
                ->end()
                ->arrayNode('statsd')
                    ->children()
                        ->booleanNode('enable')->defaultValue(false)->end()
                        ->scalarNode('host')->end()
                        ->scalarNode('port')->end()
                        ->scalarNode('prefix')->defaultValue('socloz_monitoring')->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
