<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\Profiler\Parser;
use Socloz\MonitoringBundle\Profiler\Probe;

/**
 * A XHprof data parser
 */
class Parser implements ParserInterface
{
    protected $name;
    protected $type;
    protected $calls;

    protected $time = 0;
    protected $count = 0;

    /**
     * Initializes probes
     *
     * @param Probe[] $probes
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

    /**
     * @param Probe[] $probes
     * @param array $callData
     */
    public function addCallData($probes, $callData)
    {
        foreach ($probes as $probe) {
            $probe->addCallData($callData);
        }
    }
}
