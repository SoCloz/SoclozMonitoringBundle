<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Loads all request_id adapters
 */
class AdapterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('socloz_monitoring.request_id')) {
            return;
        }
        if ($container->hasDefinition('guzzle.service_builder')) {
            $definition = $container->getDefinition('guzzle.service_builder');
            $definition->addMethodCall(
                'addGlobalPlugin',
                array(
                    new Reference("socloz_monitoring.request_id.adapter.guzzle"),
                )
            );
        }
    }
}
