<?php

namespace Socloz\MonitoringBundle\Profiler;

/**
 * A XHprof data parser
 *
 * @author jfbus
 */
class Parser
{
    
    protected $name;
    protected $type;
    protected $calls;
    
    protected $time = 0;
    protected $count = 0;

    /**
     * Initializes probes
     * 
     * @param array $probes
     */
    public function __construct($probes)
    {
        $this->calls = array();
        foreach ($probes as $probe) {
            $calls = $probe->getCalls();
            $type = $probe->getType();
            foreach ($calls as $call) {
                @$this->calls[$type][$call][] = $probe;
            }
        }
    }

    /**
     * Parses Xhprof data
     * 
     * @param array $xhprof_data
     */
    public function parse($xhprof_data)
    {
        foreach ($xhprof_data as $call => $callData) {
            $pos = strpos($call, "==>");
            if (!$pos) {
                return;
            }
            $callee = substr($call, $pos+3);

            if (isset($this->calls["callee"][$callee])) {
                $this->addCallData($this->calls["callee"][$callee], $callData);
            }
            $pos = strpos($callee, '::');
            if ($pos) {
                $class = substr($callee, 0, $pos);
                if (isset($this->calls["callee_class"][$class])) {
                    $this->addCallData($this->calls["callee_class"][$class], $callData);
                }
            }
        }
    }
    
    public function addCallData($probes, $callData)
    {
        foreach ($probes as $probe) {
            $probe->addCallData($callData);
        }
    } 
}
