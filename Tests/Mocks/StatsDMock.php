<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\MonitoringBundle\Tests\Mocks;

/**
 * Description of StatsDMock
 *
 * @author jfb
 */
class StatsDMock {

    protected $stats = array();
    
    public function __construct($host, $port, $prefix) {
    }

    public function sendException(Request $request, \Exception $exception) {
        $this->exceptions[] = $exceptions;
    }
    
    public function getExceptions() {
        return $this->exceptions;
    }
    
    public function timing($stat, $time, $sampleRate=1) {
        $this->stats["timing.$stat"] = $time;
    }
    
    public function increment($stats, $sampleRate=1) {
        $this->updateStats($stats);
    }
    
    public function updateStats($stats, $delta=1, $sampleRate=1) {
        if (!is_array($stats)) { $stats = array($stats); }
        foreach ($stats as $stat) {
            @$this->stats["counter.$stat"]+=$delta;
        }
    }
    
    public function getStats() {
        return $this->stats;
    }

}
