<?php

namespace Socloz\MonitoringBundle\Tests\Profiler;

use Socloz\MonitoringBundle\Tests\WebTestCase;

class RequestTest extends WebTestCase
{
    public function testTiming()
    {
        $client = $this->createClient();
        $statsd = $this->getContainer()->get('socloz_monitoring.statsd');
        try {
            $crawler = $client->request('GET', '/socloz_monitoring/timing/5');
        } catch (\Exception $e) {
        }
        $stats = $statsd->getStats();
        $this->assertEquals(1, $stats['counter.request']);
        $this->assertEquals(1, $stats['counter.per_route.request.test_controller_timing']);
        $this->assertEquals(5, round($stats['timing.request']/1000));
    }

    public function testCalls()
    {
        $client = $this->createClient();
        $statsd = $this->getContainer()->get('socloz_monitoring.statsd');
        try {
            $crawler = $client->request('GET', '/socloz_monitoring/preg/5');
        } catch (\Exception $e) {
        }
        $stats = $statsd->getStats();
        $this->assertEquals(5, $stats['counter.calls']);
        $this->assertEquals(5, $stats['counter.per_route.calls.test_controller_preg']);
    }
}
