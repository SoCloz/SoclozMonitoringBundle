<?php

namespace Socloz\MonitoringBundle\Tests\RequestId\Monolog;

use Socloz\MonitoringBundle\RequestId\Generator;
use Socloz\MonitoringBundle\RequestId\Monolog\Processor;
use Socloz\MonitoringBundle\RequestId\RequestId;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $requestId = new RequestId(new Generator(), true);
        $processor = new Processor($requestId, true);

        $extra = $processor(array());

        $this->assertEquals(
            array('extra' => array(
                    'request_id' => $requestId->getRequestId(),
                    'pid' => $requestId->getPid(),
                ),
            ),
            $extra
        );
    }
}
