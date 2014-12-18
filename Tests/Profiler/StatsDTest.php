<?php

namespace Socloz\MonitoringBundle\Tests\Profiler;

use Socloz\MonitoringBundle\Tests\Mocks\StatsDMock as StatsD;

class StatsDTest extends \PHPUnit_Framework_TestCase
{
    const PACKET_SIZE = 100;

    public function testAlwaysFlush()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', true, true, self::PACKET_SIZE);
        $statsd->updateStats('counter1', 1);
        $this->assertEquals(1, count($statsd->getSent()), "should have sent something");
        $statsd->updateStats('counter2', 1);
        $this->assertEquals(2, count($statsd->getSent()), "should have sent something");
        $this->assertEquals(array("prefix.counter.counter1:1|c", "prefix.counter.counter2:1|c"), $statsd->getSent(), "flush should send both counters");
    }

    public function testWithoutPacketsMerge()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, false, self::PACKET_SIZE);
        $statsd->updateStats('counter1', 1);
        $statsd->updateStats('counter2', 1);
        $this->assertEquals(0, count($statsd->getSent()), "should not have sent anything");
        $statsd->flush();
        $sent = $statsd->getSent();
        $this->assertEquals(2, count($sent), "flush should send something");
        $this->assertEquals(array("prefix.counter.counter1:1|c", "prefix.counter.counter2:1|c"), $sent, "flush should send both counters");
    }

    public function testDoNotTrack()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, true, self::PACKET_SIZE);

        $statsd->doNotTrack();
        $statsd->updateStats('counter1', 1);
        $statsd->updateStats('counter2', 1);

        $statsd->flush();
        $this->assertEquals(0, count($statsd->getSent()), "should have sent something");

        $this->assertTrue($statsd->getDoNotTrack());
    }

    /**
     * TODO REFACTOR
     */
    public function testGauge()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, true, self::PACKET_SIZE);
        $statsd->gauge(array('counter1', 'counter2'), 1);
        $statsd->flush();
        $sent = $statsd->getSent();
        $this->assertEquals(1, count($sent), "flush should send something");
        $this->assertEquals("prefix.counter1:1|c\nprefix.counter2:1|c", $sent[0], "flush should send both counters");
    }

    /**
     * TODO REFACTOR
     */
    public function testSet()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, true, self::PACKET_SIZE);
        $statsd->set('counter1', 1);
        $statsd->set('counter2', 1);
        $statsd->flush();
        $sent = $statsd->getSent();
        $this->assertEquals(1, count($sent), "flush should send something");
        $this->assertEquals("prefix.counter.counter1:1|c\nprefix.counter.counter2:1|c", $sent[0], "flush should send both counters");
    }

    public function testIncrement()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, true, self::PACKET_SIZE);
        $statsd->increment('counter1', 1);
        $statsd->increment('counter2', 1);
        $this->assertEquals(0, count($statsd->getSent()), "should not have sent anything");
        $statsd->flush();
        $sent = $statsd->getSent();
        $this->assertEquals(1, count($sent), "flush should send something");
        $this->assertEquals("prefix.counter.counter1:1|c\nprefix.counter.counter2:1|c", $sent[0], "flush should send both counters");
    }

    public function testDecrement()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, true, self::PACKET_SIZE);
        $statsd->decrement('counter1', 1);
        $statsd->decrement('counter2', 1);
        $this->assertEquals(0, count($statsd->getSent()), "should not have sent anything");
        $statsd->flush();
        $sent = $statsd->getSent();
        $this->assertEquals(1, count($sent), "flush should send something");
        $this->assertEquals("prefix.counter.counter1:-1|c\nprefix.counter.counter2:-1|c", $sent[0], "flush should send both counters");
    }

    public function testFlush()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, true, self::PACKET_SIZE);
        $statsd->updateStats('counter1', 1);
        $statsd->updateStats('counter2', 1);
        $this->assertEquals(0, count($statsd->getSent()), "should not have sent anything");
        $statsd->flush();
        $sent = $statsd->getSent();
        $this->assertEquals(1, count($sent), "flush should send something");
        $this->assertEquals("prefix.counter.counter1:1|c\nprefix.counter.counter2:1|c", $sent[0], "flush should send both counters");
    }

    public function testPacketSize()
    {
        $statsd = new StatsD('localhost', 42, 'prefix', false, true, self::PACKET_SIZE);
        $statsd->updateStats('counter', 1);
        $statsd->flush();
        $sent = $statsd->getSent();
        $this->assertEquals(1, count($sent), "should have sent something");
        $msgLen = strlen($sent[0]);
        $maxCount = ceil(self::PACKET_SIZE / $msgLen + 1) + 1;
        for ($i = 0; $i < $maxCount; $i++) {
            $statsd->updateStats('counter', 1);
        }
        $sent = $statsd->getSent();
        $this->assertEquals(2, count($sent), "packet size overflow => should send a packet");
    }
}
