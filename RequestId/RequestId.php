<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\RequestId;

/**
 * RequestId service
 */
class RequestId
{

    /**
     * @var string
     */
    protected $requestId;

    /**
     * @var int
     */
    protected $pid;

    /**
     * @param Generator $generator
     * @param           $addPid
     */
    public function __construct(Generator $generator, $addPid)
    {
        $this->requestId = $generator->getRequestId();
        if ($addPid) {
            $this->pid = getmypid();
        }
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }
}