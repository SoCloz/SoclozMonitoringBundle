<?php

namespace Socloz\MonitoringBundle\Tests\Mocks;

use Socloz\MonitoringBundle\Notify\StatsD;

/**
 * StatsD Mock
 *
 * @author jfb
 */
class StatsDMock extends StatsD
{
    protected $stats = array();
    protected $sent = array();

    public function timing($stat, $time, $sampleRate = 1)
    {
        parent::timing("timing.$stat", floor($time/1000), $sampleRate);
    }

    public function updateStats($stat, $delta = 1, $sampleRate = 1)
    {
        if (is_array($stat)) {
            foreach ($stat as $k => $v)
            {
                $stat['counter.'.$k] = $v;
                unset($stat[$k]);
            }
        } else {
            $stat = 'counter.' . $stat;
        }
        parent::updateStats($stat, $delta, $sampleRate);
    }

    public function getStats()
    {
        return $this->stats;
    }

    protected function queue($data, $sampleRate = 1)
    {
        $this->stats = array_merge($this->stats, $data);
        parent::queue($data, $sampleRate);
    }

    protected function send($data)
    {
        $this->sent[] = $data;
    }

    public function getSent()
    {
        return $this->sent;
    }

    public function getDoNotTrack()
    {
        return $this->doNotTrack;
    }
}
