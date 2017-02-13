<?php

namespace Socloz\MonitoringBundle\Tests\Notify\Message;

use Socloz\MonitoringBundle\Notify\Message\DefaultMessage;
use Symfony\Component\HttpFoundation\Request;

class DefaultMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageContent()
    {
        $request = Request::create('http://localhost.com');
        $exception = new \Exception('My exception message');
        $mailerTransformer = $this->getMockBuilder('Socloz\MonitoringBundle\Transformer\MailerTransformer')->getMock();

        $templating = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')->getMock();
        $templating->method('render')
            ->willReturn('My email exception content');

        $message = new DefaultMessage($templating, $mailerTransformer, $request, $exception);
        $this->assertEquals('Error message from localhost.com - My exception message', $message->getSubject());
        $this->assertEquals('My email exception content', $message->getContent());
        $this->assertEquals('text/html', $message->getContentType());
    }
}
