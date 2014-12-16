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
}
