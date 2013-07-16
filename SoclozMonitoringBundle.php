<?php

namespace Socloz\MonitoringBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Socloz\MonitoringBundle\DependencyInjection\AdapterCompilerPass;

class SoclozMonitoringBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AdapterCompilerPass());
    }
}
