<?php

namespace Socloz\MonitoringBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class SoclozMonitoringExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        foreach ($config as $key => $subconfig) {
            foreach ($subconfig as $subkey => $value) {
                $container->setParameter($this->getAlias().'.'.$key.'.'.$subkey, $value);
            }
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));


        foreach (array("mailer", "statsd", "exceptions", "profiler") as $module) {
            if ($config[$module]['enable']) {
                $loader->load("$module.xml");
            }
        }
        if ($config['profiler']['enable']) {
            $parsers = array();
            foreach ($config['profiler'] as $key => $value) {
                if ($key == 'enable' || $key == "sampling" || !$value) { continue; }
                $parsers[] = $this->createProfilerParser($key, $container);
            }
            $container->getDefinition('socloz_monitoring.profiler')
                ->replaceArgument(0, $parsers);
        }

    }
    
    /**
     * Generates a parser service for a configured parser
     * 
     * @param string $name
     * @param ContainerBuilder $container
     * @return \Symfony\Component\DependencyInjection\Reference 
     */
    private function createProfilerParser($name, ContainerBuilder $container)
    {
        $definition = $container->getParameter(sprintf("socloz_monitoring.profiler.parser.definition.%s", $name));
        $id = sprintf('socloz_monitoring.profiler.%s_parser', $name);
        
        $container
            ->setDefinition($id, new DefinitionDecorator('socloz_monitoring.profiler.parser'))
            ->replaceArgument(0, $name)
            ->replaceArgument(1, $definition)
            ->addTag('socloz_monitoring.profiler.parser')
        ;
        return new Reference($id);
    }

    public function getAlias()
    {
        return 'socloz_monitoring';
    }

}
