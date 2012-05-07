<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\MonitoringBundle\Profiler;

/**
 * Description of Parser
 *
 * @author jfb
 */
class Parser {
    
    protected $name;
    protected $type;
    protected $calls;
    
    protected $time = 0;
    protected $count = 0;

    public function __construct($name, $definition)
    {
        $this->name = $name;
        $this->type = isset($definition['type']) ? $definition['type'] : "call";
        $calls = isset($definition['calls']) ? $definition['calls'] : array();
        foreach ($calls as $call) {
            $this->calls[$call] = true;
        }
    }

    public function parse($data) {
        if ($this->type == "call") {
            foreach (array_keys($this->calls) as $call) {
                if (isset($data[$call])) {
                    $this->addCallData($data[$call]);
                }
            }
        } else {
            foreach ($data as $callerCallee => $callData) {
                $callArr = explode("==>", $callerCallee);
                if (count($callArr) != 2) {
                    continue;
                }
                $call = ($this->type == "caller" ? $callArr[0] : $callArr[1]);
                if (isset($this->calls[$call])) {
                    $this->addCallData($callData);
                }
            }
        }
    }
    
    public function addCallData($callData) {
        $this->time += $callData['wt']/1000; // ms
        $this->count++;
    }
    
    public function getTime() {
        return $this->time;
    }
    
    public function getCount() {
        return $this->count;
    }
    
    public function getName() {
        return $this->name;
    }
}
