<?php


namespace Socloz\MonitoringBundle\Notify\StatsD;


interface StatsDInterface
{
    public function timing($stat, $time, $sampleRate = 1);

    public function gauge($stats, $value);

    public function set($stats, $value);

    public function increment($stats, $sampleRate = 1);

    public function decrement($stats, $sampleRate = 1);

    public function updateStats($stats, $delta = 1, $sampleRate = 1);

    public function flush();
}