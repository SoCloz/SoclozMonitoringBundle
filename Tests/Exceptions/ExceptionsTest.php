<?php

namespace Socloz\MonitoringBundle\Tests\Exceptions;

use Socloz\MonitoringBundle\Tests\WebTestCase;

class ExceptionsTest extends WebTestCase
{
    protected $mailer;
    protected $statsd;

    public function testException()
    {
        $client = $this->createClient();
        $mailer = $this->getContainer()->get('socloz_monitoring.mailer');
        $statsd = $this->getContainer()->get('socloz_monitoring.statsd');

        $e = null;
        try {
            $client->request('GET', '/socloz_monitoring/exception/Exception');
        } catch (\Exception $e) {
        }
        $this->assertNotNull($e);
        $exceptions = $mailer->getExceptions();
        $this->assertEquals(1, count($exceptions));
        $this->assertEquals("Generated exception", $exceptions[0]->getMessage());

        $stats = $statsd->getStats();
        $this->assertEquals("1|c", $stats['counter.exception']);
    }

    public function testIgnoredException()
    {
        $client = $this->createClient();
        $statsd = $this->getContainer()->get('socloz_monitoring.statsd');
        $exceptionListener = $this->getContainer()->get('socloz_monitoring.listener.exceptions');

        $ref = new \ReflectionClass('\Socloz\MonitoringBundle\Listener\Exceptions');
        $refProp = $ref->getProperty('ignore');
        $refProp->setAccessible(true);
        $refProp->setValue($exceptionListener, array('\Exception'));

        $e = null;
        try {
            $client->request('GET', '/socloz_monitoring/exception/Exception');
        } catch (\Exception $e) {
        }

        $stats = $statsd->getStats();
        $this->assertEquals(false, isset($stats['counter.exception']));

        $e = null;
        try {
            $client->request('GET', '/socloz_monitoring/exception/Socloz__MonitoringBundle__Tests__Fixtures__Exception__ExceptionChild');
        } catch (\Exception $e) {
        }

        $stats = $statsd->getStats();
        $this->assertEquals(false, isset($stats['counter.exception']));
    }
}
