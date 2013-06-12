<?php

namespace Socloz\MonitoringBundle\Notify;

use Symfony\Component\HttpFoundation\Request;

/**
 * Logs profiling data
 *
 * @author jfb
 */
class Logger
{
    
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
        
    }
    
    public function log(Request $request = null, array $timing, array $calls)
    {
        $msg = sprintf("%s : %d ms", ($request ? $request->getRequestUri() : "-"), $timing['request']);
        foreach ($calls as $probe => $value) {
            if ($probe != "request") {
                $msg .= sprintf(" %s : %d calls/%d ms", $probe, $value, isset($timing[$probe]) ? $timing[$probe] : 0);
            }
        }
        $this->logger->info($msg);
    }

}
