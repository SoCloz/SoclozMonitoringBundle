<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\RequestId\Adapters;

use Guzzle\Common\Event;
use Socloz\MonitoringBundle\RequestId\RequestId;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Inserts requestId as a HTTP header to all Guzzle calls
 */
class Guzzle implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $requestId;

    /**
     * @param RequestId $requestId
     */
    public function __construct(RequestId $requestId)
    {
        $this->requestId = $requestId->getRequestId();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => array('onRequestBeforeSend', 125),
        );
    }

    /**
     * Add X-RequestId header to outgoing request
     *
     * @param Event $event
     */
    public function onRequestBeforeSend(Event $event)
    {
        $request = $event['request'];
        $request->addHeader("X-RequestId", $this->requestId);
    }
}
