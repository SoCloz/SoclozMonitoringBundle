<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\RequestId\Monolog;

use Socloz\MonitoringBundle\RequestId\RequestId;

/**
 * Monolog processor - adds requestId & pid to log lines
 */
class Processor
{
    protected $requestId;
    protected $addPid;

    /**
     * @param RequestId $requestId
     * @param           $addPid
     */
    public function __construct(RequestId $requestId, $addPid)
    {
        $this->requestId = $requestId;
        $this->addPid = $addPid;
    }

    /**
     * Adds request_id & pid to log line
     *
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        $record['extra']['request_id'] = $this->requestId->getRequestId();
        if ($this->addPid) {
            $record['extra']['pid'] = $this->requestId->getPid();
        }

        return $record;
    }
}
