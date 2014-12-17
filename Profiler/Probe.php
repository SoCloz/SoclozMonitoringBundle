<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\Profiler;

/**
 * A XHprof data parser
 */
class Probe
{
    const TRACKER_TIMING = 1;
    const TRACKER_CALLS = 2;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $tracker;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $calls;

    /**
     * @var int
     */
    protected $time = 0;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @param string $name
     * @param int $tracker
     * @param array  $definition
     */
    public function __construct($name, $tracker, array $definition = array())
    {
        $this->name = $name;
        $this->tracker = $tracker;
        $this->type = isset($definition['type']) ? $definition['type'] : "call";
        $this->calls = isset($definition['calls']) ? $definition['calls'] : array();
    }

    /**
     * @return int
     */
    public function isTimingProbe()
    {
        return $this->tracker&self::TRACKER_TIMING;
    }

    /**
     * @return int
     */
    public function isCallsProbe()
    {
        return $this->tracker&self::TRACKER_CALLS;
    }

    /**
     * Adds timing/count data
     *
     * @param array $callData
     */
    public function addCallData(array $callData)
    {
        if ($this->isTimingProbe()) {
            $this->time += (int) $callData['wt']/1000; // ms
        }
        if ($this->isCallsProbe()) {
            $this->count += $callData['ct'];
        }
    }

    /**
     * Get total wall time for current parser
     *
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Get total number of calls for current parser
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Get the parser name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the probe type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the probe type
     *
     * @return array
     */
    public function getCalls()
    {
        return $this->calls;
    }
}
