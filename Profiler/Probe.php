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
    
    protected $name;
    protected $tracker;
    protected $type;
    protected $calls;
    
    protected $time = 0;
    protected $count = 0;

    public function __construct($name, $tracker, $definition)
    {
        $this->name = $name;
        $this->tracker = $tracker;
        $this->type = isset($definition['type']) ? $definition['type'] : "call";
        $this->calls = isset($definition['calls']) ? $definition['calls'] : array();
    }

    public function isTimingProbe()
    {
        return $this->tracker&self::TRACKER_TIMING;
    }
    
    public function isCallsProbe()
    {
        return $this->tracker&self::TRACKER_CALLS;
    }
    
    /**
     * Adds timing/count data
     * 
     * @param array $callData 
     */
    public function addCallData($callData)
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
    public function getCount() {
        return $this->count;
    }
    
    /**
     * Get the parser name
     * 
     * @return string 
     */
    public function getName() {
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
     * @return string
     */
    public function getCalls()
    {
        return $this->calls;
    }
}
