<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enable')->defaultValue(true)->end()
                        ->variableNode('ignore')->defaultValue(array('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', 'Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException'))->end()
                    ->end()
                ->end()
                ->arrayNode('profiler')
                    ->addDefaultsIfNotSet()
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enable')->defaultValue(true)->end()
                        ->scalarNode('from')->end()
                        ->scalarNode('to')->end()
                    ->end()
                ->end()
                ->arrayNode('statsd')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enable')->defaultValue(false)->end()
                        ->scalarNode('host')->end()
                        ->scalarNode('port')->end()
                        ->scalarNode('prefix')->defaultValue('socloz_monitoring')->end()
                        ->booleanNode('always_flush')->defaultValue(false)->end()
                        ->booleanNode('merge_packets')->defaultValue(false)->end()
                        // Assuming we are on a LAN
                        ->scalarNode('packet_size')->defaultValue(1432)->end()
                    ->end()
                ->end()
                ->arrayNode('request_id')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enable')->defaultValue(true)->end()
                        ->booleanNode('add_pid')->defaultValue(true)->end()
                    ->end()
                ->end()
                ->arrayNode('logger')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enable')->defaultValue(false)->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
