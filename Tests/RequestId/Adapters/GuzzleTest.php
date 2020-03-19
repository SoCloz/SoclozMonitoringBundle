<?php

namespace Socloz\MonitoringBundle\Tests\RequestId\Adapters;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Socloz\MonitoringBundle\RequestId\Adapters\Guzzle;
use Socloz\MonitoringBundle\RequestId\Generator;
use Socloz\MonitoringBundle\RequestId\RequestId;

class GuzzleTest extends \PHPUnit_Framework_TestCase
{
    public function testSubscribedEvents()
    {
        $requestId = new RequestId(new Generator(), true);
        $guzzzleAdapter = new Guzzle($requestId);

        $this->assertEquals(
            array('request.before_send' => array('onRequestBeforeSend', 125)),
            $guzzzleAdapter->getSubscribedEvents()
        );
    }

    public function testOnRequestBeforeSend()
    {
        $requestId = new RequestId(new Generator(), true);
        $guzzzleAdapter = new Guzzle($requestId);

        $event = new Event();
        $request = new Request('GET', 'test');
        $event->offsetSet('request', $request);
        $guzzzleAdapter->onRequestBeforeSend($event);

        $this->assertEquals($requestId->getRequestId(), $event['request']->getHeader("X-RequestId"));
    }
}
