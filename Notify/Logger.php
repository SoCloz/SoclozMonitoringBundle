<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\Notify;

use Symfony\Component\HttpFoundation\Request;

/**
 * Logs profiling data
 */
class Logger
{
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param array   $timing
     * @param array   $calls
     */
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
