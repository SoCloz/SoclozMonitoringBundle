<?php

namespace Socloz\MonitoringBundle\Profiler;

/**
 * Xhprof profiler
 *
 * @author jfb
 */
class Xhprof {

    protected $profiling;
    
    protected $mailer;
    protected $statsd;
    
    protected $parsers;
    protected $memory;
    
    protected $timers = array();
    protected $counters = array();
    
    public function __construct($parsers, $memory) {
        $this->parsers = $parsers;
        $this->memory = $memory;
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

    }

    /**
     * Stops the profiling & parses data
     */
    public function stopProfiling()
    {
        if (!$this->profiling) {
            return;
        }

        $this->profiling = false;
        $xhprof_data = xhprof_disable();
        if (is_array($xhprof_data)) {
            foreach ($xhprof_data as $call => $callData) {
                foreach ($this->parsers as $parser) {
                    $parser->match($call, $callData);
                }
            }
        }
        foreach ($this->parsers as $parser) {
            $name = $parser->getName();
            $this->timers[$name] = $parser->getTime();
            $this->counters[$name] = $parser->getCount();
        }
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
