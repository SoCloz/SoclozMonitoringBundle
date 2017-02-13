<?php

namespace Socloz\MonitoringBundle\Tests\Notify\Message;

use Socloz\MonitoringBundle\Notify\Message\DefaultMessageFactory;
use Symfony\Component\HttpFoundation\Request;

class DefaultMessageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $request = Request::create('http://localhost.com');
        $exception = new \Exception('My exception message');
        $templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')->getMock();
        $mailerTransformer = $this->getMockBuilder('Socloz\MonitoringBundle\Transformer\MailerTransformer')->getMock();

        $factory = new DefaultMessageFactory($templating, $mailerTransformer);
        $message = $factory->create($request, $exception);
        $this->assertInstanceOf('Socloz\MonitoringBundle\Notify\Message\MessageInterface', $message);
        $this->assertInstanceOf('Socloz\MonitoringBundle\Notify\Message\DefaultMessage', $message);
    }
}
