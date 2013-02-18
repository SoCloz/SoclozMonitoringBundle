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
 * @author jfbus <jf@closetome.fr>
 */
class Profiler
{
    protected $profiler;
    protected $statsd;
    protected $sampling;

    public function __construct($profiler, $statsd, $sampling)
    {
        $this->profiler = $profiler;
        $this->statsd = $statsd;
        $this->sampling = $sampling;
    }

    public function onCoreRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($this->sampling != 100 && mt_rand(1, 100) > $this->sampling) {
                return;
            }
            $this->profiler->startProfiling();
        }
    }

    public function onCoreResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($this->profiler->stopProfiling()) {
                $timers = $this->profiler->getTimers();
                $counters = $this->profiler->getCounters();
                if ($this->statsd) {
                    foreach ($timers as $key => $value) {
                        $this->statsd->timing($key, $value, $this->sampling/100);
                    }
                    foreach ($counters as $key => $value) {
                        $this->statsd->updateStats($key, $value, $this->sampling/100);
                    }
                }
            }
        }
    }
}
