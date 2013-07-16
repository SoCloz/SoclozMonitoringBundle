<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\RequestId;

use Socloz\MonitoringBundle\RequestId\Adapters\AdapterInterface;

/**
 * RequestId service
 */
class RequestId
{

    protected $requestId;
    protected $pid;

    public function __construct(Generator $generator, $addPid)
    {
        $this->requestId = $generator->getRequestId();
        if ($addPid) {
            $this->pid = getmypid();
        }
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getPid()
    {
        return $this->pid;
    }
}