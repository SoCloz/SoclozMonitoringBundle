<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jfb
 * Date: 12/06/13
 * Time: 11:21
 * To change this template use File | Settings | File Templates.
 */

namespace Socloz\MonitoringBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

use Socloz\MonitoringBundle\Tests\Fixtures\TestKernel;

abstract class WebTestCase extends BaseWebTestCase
{

    protected function getContainer(array $options = array())
    {
        if (!static::$kernel) {
            static::$kernel = static::createKernel($options);
        }
        static::$kernel->boot();

        return static::$kernel->getContainer();
    }

    protected static function createKernel(array $options = array())
    {
        require_once __DIR__.'/Fixtures/app/TestKernel.php';

        return new TestKernel(
            'default',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}