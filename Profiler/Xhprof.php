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
 * Xhprof profiler
 */
class Xhprof {

    protected $profiling;
    
    protected $mailer;
    protected $statsd;
    
    protected $parser;
    protected $probes;
    protected $memory;
    
    protected $timers = array();
    protected $counters = array();
    
    protected $start;
    
    public function __construct($parserClass, $probes, $memory) {
        $this->parser = new $parserClass($probes);
        $this->probes = $probes;
        $this->memory = $memory;
        $this->counters['request'] = 1;
    }
    
    /**
     * Starts the profiling 
     */
    public function startProfiling()
    {
        if (PHP_SAPI == 'cli') {
            $_SERVER['REMOTE_ADDR'] = null;
            $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        }

        if (function_exists('xhprof_enable')) {            
            $this->profiling = true;
            xhprof_enable($this->memory ? XHPROF_FLAGS_MEMORY : null);
        }
        $this->start = microtime(true);
    }

    /**
     * Stops the profiling & parses data
     * @return boolean $enabled
     */
    public function stopProfiling()
    {
        if (!$this->profiling) {
            return false;
        }
        $requestTime = microtime(true) - $this->start;
        $this->timers['request'] = (int) ($requestTime*1000);
        
        $this->profiling = false;
        $xhprof_data = xhprof_disable();
        if (is_array($xhprof_data)) {
            $this->parser->parse($xhprof_data);
        }
        foreach ($this->probes as $probe) {
            $name = $probe->getName();
            if ($probe->isTimingProbe()) {
                $this->timers[$name] = $probe->getTime();
            }
            if ($probe->isCallsProbe()) {
                $this->counters[$name] = $probe->getCount();
            }
        }
        
        return true;
    }

    /**
     * Returns the list of timers
     * 
     * @return array 
     */
    public function getTimers() {
        return $this->timers;
    }
    
    /**
     * Returns the list of counters
     * 
     * @return array 
     */
    public function getCounters() {
        return $this->counters;
    }
}
