<?php

namespace Socloz\MonitoringBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestTest extends WebTestCase
{
    protected $client;
    protected $mailer;
    protected $statsd;

    public function __construct() {
        
        $kernel = self::createKernel();
        $kernel->boot();
        $this->client = $kernel->getContainer()->get('test.client');
        $this->mailer = $kernel->getContainer()->get('socloz_monitoring.mailer');
        $this->statsd = $kernel->getContainer()->get('socloz_monitoring.statsd');

    }
    
    public function testException()
    {
        $e = null;
        try {
            $this->client->request('GET', '/socloz_monitoring/exception/Exception');
        } catch (\Exception $e) {
        }
        $this->assertNotNull($e);
        $exceptions = $this->mailer->getExceptions();
        $this->assertEquals(1, count($exceptions));
        $this->assertEquals("Generated exception", $exceptions[0]->getMessage());
    }

    public function testTiming()
    {
        $this->fail('Needs update');
        try {
            $crawler = $this->client->request('GET', '/socloz_monitoring/timing/5');
        } catch (\Exception $e) {
            
        }
        $stats = $this->statsd->getStats();
        $this->assertEquals(1, $stats['counter.request']);
        $this->assertEquals(5, round($stats['timing.request']/1000));
    }
}
