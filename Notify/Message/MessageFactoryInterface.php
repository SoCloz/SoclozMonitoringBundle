<?php

namespace Socloz\MonitoringBundle\Notify\Message;

use Symfony\Component\HttpFoundation\Request;

interface MessageFactoryInterface
{
    /**
     * Create message instance
     *
     * @param Request    $request
     * @param \Exception $exception
     * @return MessageInterface
     */
    public function create(Request $request, \Exception $exception);
}
