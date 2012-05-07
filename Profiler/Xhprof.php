<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\MonitoringBundle\Profiler;

/**
 * Description of Xhprof
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

    public function stopProfiling()
    {
        if (!$this->profiling) {
            return;
        }

        $this->profiling = false;
        $xhprof_data = xhprof_disable();
        foreach ($this->parsers as $parser) {
            $parser->parse($xhprof_data);
            $name = $parser->getName();
            $this->timers[$name] = $parser->getTime();
            $this->counters[$name] = $parser->getCount();
        }
    }

    public function getTimers() {
        return $this->timers;
    }
    
    public function getCounters() {
        return $this->counters;
    }
}
