<?php

namespace Socloz\MonitoringBundle\Listener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * RequestListener.
 *
 * The handle method must be connected to the core.request event.
 *
 * @author Jonas Wouters <hello@jonaswouters.be>
 */
class Profiler
{
    protected $profiler;
    protected $statsd;

    public function __construct($profiler, $statsd)
    {
        $this->profiler = $profiler;
        $this->statsd = $statsd;
    }

    public function onCoreRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->profiler->startProfiling();
        }
    }

    public function onCoreResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->profiler->stopProfiling();
            $timers = $this->profiler->getTimers();
            $counters = $this->profiler->getCounters();
            if ($this->statsd) {
                foreach ($timers as $key => $value) {
                    $this->statsd->timing($key, $value);
                }
                foreach ($counters as $key => $value) {
                    $this->statsd->updateStats($key, $value);
                }
            }
            
        }
    }
}
