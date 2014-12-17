<?php


namespace Socloz\MonitoringBundle\Profiler\Parser;

use Socloz\MonitoringBundle\Profiler\Probe;

interface ParserInterface
{
    /**
     * @param Probe[] $probes
     */
    public function __construct($probes);

    /**
     * Parses Xhprof data
     *
     * @param array $xhprof_data
     */
    public function parse($xhprof_data);

    /**
     * @param Probe[] $probes
     * @param array   $callData
     */
    public function addCallData($probes, $callData);
}
