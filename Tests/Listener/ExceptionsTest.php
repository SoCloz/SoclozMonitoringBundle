<?php

namespace Socloz\MonitoringBundle\Tests\Listener;

use Socloz\MonitoringBundle\Listener\Exceptions;

class ExceptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $exceptions = new Exceptions(null, null, null);
        $this->assertEquals('Socloz\MonitoringBundle\Listener\Exceptions', get_class($exceptions));
    }
} 