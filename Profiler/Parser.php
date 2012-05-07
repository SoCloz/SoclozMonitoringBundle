<?php

namespace Socloz\MonitoringBundle\Profiler;

/**
 * A XHprof data parser
 *
 * @author jfbus
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

    /**
     * Parses Xhprof data
     * 
     * @param array $data 
     */
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
    
    /**
     * Adds timing/count data
     * 
     * @param array $callData 
     */
    public function addCallData($callData) {
        $this->time += (int) $callData['wt']/1000; // ms
        $this->count += $callData['ct'];
    }
    
    /**
     * Get total wall time for current parser
     * 
     * @return int 
     */
    public function getTime() {
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
}
