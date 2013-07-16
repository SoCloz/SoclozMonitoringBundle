<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\Listener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Socloz\MonitoringBundle\RequestId\RequestId as RequestIdService;

/**
 * Adds X-RequestId header to the http response
 */
class RequestId
{
    protected $requestId;
    protected $logger;

    public function __construct(RequestIdService $requestId, $logger)
    {
        $this->requestId = $requestId;
        $this->logger = $logger;
    }

    public function onCoreRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $request = $event->getRequest();
            if ($request->headers->has("X-RequestId")) {
                $requestId = $request->headers->get("X-RequestId");
                if ($this->logger) {
                    $this->logger->info(sprintf("MonitoringBundle : using requestId %s", $requestId));
                }
                $this->requestId->setRequestId($requestId);
            }
        }
    }

    public function onCoreResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $response = $event->getResponse();
            $response->headers->add(array("X-RequestId" => $this->requestId));
        }
    }
}
