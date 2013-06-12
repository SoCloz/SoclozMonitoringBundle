<?php

namespace Socloz\MonitoringBundle\Tests\Profiler;

use Socloz\MonitoringBundle\Tests\WebTestCase;

class ProfilerTest extends WebTestCase
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
        $this->assertEquals("1|c", $stats['counter.request']);
        $this->assertEquals("1|c", $stats['counter.per_route.request.test_controller_timing']);
        $this->assertEquals("5|ms", $stats['timing.request']);
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
        $this->assertEquals("5|c", $stats['counter.calls']);
        $this->assertEquals("5|c", $stats['counter.per_route.calls.test_controller_preg']);
    }
}
