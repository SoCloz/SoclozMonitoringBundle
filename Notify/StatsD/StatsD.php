<?php

namespace Socloz\MonitoringBundle\Notify\StatsD;

/**
 * StatsD client
 * inspired by https://github.com/etsy/statsd/blob/master/examples/php-example.php
 * @author etsy, Jean-François Bustarret
 */
class StatsD implements StatsDInterface
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var bool
     */
    protected $alwaysFlush;

    /**
     * @var bool
     */
    protected $mergePackets;

    /**
     * @var int
     */
    protected $packetSize;

    /**
     * @var bool
     */
    protected $doNotTrack = false;

    /**
     * @var int
     */
    protected $queueSize = 0;

    /**
     * @var array
     */
    protected $queue = array();

    /**
     * @param string  $host
     * @param int     $port
     * @param string  $prefix
     * @param boolean $alwaysFlush
     * @param boolean $mergePackets
     * @param int     $packetSize
     */
    public function __construct($host, $port, $prefix, $alwaysFlush, $mergePackets, $packetSize)
    {
        $this->host = $host;
        $this->port = $port;
        $this->prefix = $prefix;
        $this->alwaysFlush = $alwaysFlush;
        $this->mergePackets = $mergePackets;
        $this->packetSize = $packetSize;
    }

    public function doNotTrack()
    {
        $this->doNotTrack = true;
    }

    public function __destruct()
    {
        $this->flush();
    }

    /**
     * Log timing information
     *
     * @param string  $stats      The metric to in log timing info for.
     * @param float   $time       The ellapsed time (ms) to log
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     **/
    public function timing($stat, $time, $sampleRate = 1)
    {
        $this->queue(array($stat => "$time|ms"), $sampleRate);
    }

    /**
     * Sets one or more gauges to a value
     *
     * @param string|array $stats The metric(s) to set.
     * @param float        $value The value for the stats.
     **/
    public function gauge($stats, $value)
    {
        $this->updateStats($stats, $value, 1, 'g');
    }

    /**
     * A "Set" is a count of unique events.
     * This data type acts like a counter, but supports counting
     * of unique occurences of values between flushes. The backend
     * receives the number of unique events that happened since
     * the last flush.
     * The reference use case involved tracking the number of active
     * and logged in users by sending the current userId of a user
     * with each request with a key of "uniques" (or similar).
     *
     * @param string|array $stats The metric(s) to set.
     * @param float        $value The value for the stats.
     **/
    public function set($stats, $value)
    {
        $this->updateStats($stats, $value, 1, 's');
    }

    /**
     * Increments one or more stats counters
     *
     * @param string|array $stats      The metric(s) to increment.
     * @param float|1      $sampleRate the rate (0-1) for sampling.
     *
     * @return boolean
     **/
    public function increment($stats, $sampleRate = 1)
    {
        $this->updateStats($stats, 1, $sampleRate);
    }

    /**
     * Decrements one or more stats counters.
     *
     * @param string|array $stats      The metric(s) to decrement.
     * @param float|1      $sampleRate the rate (0-1) for sampling.
     *
     * @return boolean
     **/
    public function decrement($stats, $sampleRate = 1)
    {
        $this->updateStats($stats, -1, $sampleRate);
    }

    /**
     * Updates one or more stats counters by arbitrary amounts.
     *
     * @param string|array $stats      The metric(s) to update. Should be either a string or array of metrics.
     * @param int|1        $delta      The amount to increment/decrement each metric by.
     * @param float|1      $sampleRate the rate (0-1) for sampling.
     *
     * @return boolean
     **/
    public function updateStats($stats, $delta = 1, $sampleRate = 1)
    {
        if (!is_array($stats)) {
            $stats = array($stats);
        }
        $data = array();
        foreach ($stats as $stat) {
            $data[$stat] = "$delta|c";
        }

        $this->queue($data, $sampleRate);
    }

    /**
     * queues data
     *
     * @param array $data
     * @param int   $sampleRate
     */
    protected function queue($data, $sampleRate = 1)
    {
        if ($sampleRate < 1) {
            foreach ($data as $stat => $value) {
                $data[$stat] = "$value|@$sampleRate";
            }
        }
        foreach ($data as $stat => $value) {
            $msg = "$this->prefix.$stat:$value";
            if ($this->queueSize + strlen($msg) > $this->packetSize) {
                $this->flush();
            }
            $this->queue[] = $msg;
            $this->queueSize += strlen($msg) + 1;
        }
        if ($this->alwaysFlush) {
            $this->flush();
        }
    }

    /**
     * Flushes the queue
     */
    public function flush()
    {
        if ($this->doNotTrack) {
            return;
        }

        if (empty($this->queue)) {
            return;
        }

        if ($this->mergePackets) {
            $this->send(implode("\n", $this->queue));
        } else {
            foreach ($this->queue as $data) {
                $this->send($data);
            }
        }
        $this->queue = array();
        $this->queueSize = 0;
    }

    /**
     * Sends metrics over UDP
     */
    protected function send($data)
    {
        if ($this->doNotTrack) {
            return;
        }

        // Wrap this in a try/catch - failures in any of this should be silently ignored
        try {
            $fp = fsockopen("udp://$this->host", $this->port, $errno, $errstr);
            if (!$fp) {
                return;
            }
            fwrite($fp, $data);
            fclose($fp);
        } catch (\Exception $e) {
        }
    }
}
