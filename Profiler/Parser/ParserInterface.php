<?php


namespace Socloz\MonitoringBundle\Profiler\Parser;


interface ParserInterface
{
    public function parse($xhprof_data);

    public function addCallData($probes, $callData);
}
