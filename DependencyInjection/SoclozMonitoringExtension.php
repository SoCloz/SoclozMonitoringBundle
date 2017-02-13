<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\DependencyInjection;

use Socloz\MonitoringBundle\Profiler\Probe;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SoclozMonitoringExtension extends Extension
{
    /**
     * @var array
     */
    private $modules = array("mailer", "statsd", "exceptions", "profiler", "logger", "request_id");

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        foreach ($config as $key => $subConfig) {
            foreach ($subConfig as $subKey => $value) {
                $container->setParameter($this->getAlias().'.'.$key.'.'.$subKey, $value);
            }
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ($this->modules as $module) {
            if (isset($config[$module]['enable']) && $config[$module]['enable']) {
                $loader->load($module.'.xml');
            }
        }
        if (isset($config['profiler']['enable']) && $config['profiler']['enable']) {
            $probes = array();
            foreach ($config['profiler'] as $key => $value) {
                if (in_array($key, array('enable', 'sampling', 'request')) || !$value) {
                    continue;
                }
                $probes = array_merge($probes, $this->createProfilerProbes($key, $container));
            }
            $container->getDefinition('socloz_monitoring.profiler')
                ->replaceArgument(1, $probes);
        }

        if (isset($config['mailer']['enable']) && $config['mailer']['enable']) {
            $container->setAlias('socloz_monitoring.message_factory', $config['mailer']['message_factory']);
        }
    }

    /**
     * Generates a probe service for a configured probe
     *
     * @param string           $name
     * @param ContainerBuilder $container
     *
     * @return Reference[]
     */
    private function createProfilerProbes($name, ContainerBuilder $container)
    {
        $key = sprintf("socloz_monitoring.profiler.probe.definition.%s", $name);
        if ($container->hasParameter($key)) {
            $definition = $container->getParameter($key);

            return array($this->createProbeDefinition($name, Probe::TRACKER_CALLS | Probe::TRACKER_TIMING, $definition, $container));
        } else {
            return array(
                $this->createProbeDefinition(
                    $name,
                    Probe::TRACKER_CALLS,
                    $container->getParameter($key.'.calls'), $container
                ),
                $this->createProbeDefinition(
                    $name,
                    Probe::TRACKER_TIMING,
                    $container->getParameter($key.'.timing'),
                    $container
                ),
            );
        }
    }

    private function createProbeDefinition($name, $tracker, $definition, ContainerBuilder $container)
    {
        $id = sprintf(
            'socloz_monitoring.profiler.%s_%s_%s_probe',
            $name,
            $tracker & Probe::TRACKER_CALLS ? 'calls' : '',
            $tracker & Probe::TRACKER_TIMING ? 'timing' : ''
        );

        $container
            ->setDefinition($id, new DefinitionDecorator('socloz_monitoring.profiler.probe'))
            ->replaceArgument(0, $name)
            ->replaceArgument(1, $tracker)
            ->replaceArgument(2, $definition)
            ->addTag('socloz_monitoring.profiler.probe');

        return new Reference($id);
    }

    public function getAlias()
    {
        return 'socloz_monitoring';
    }
}
