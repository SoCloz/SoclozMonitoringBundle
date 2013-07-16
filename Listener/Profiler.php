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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * The profiler
 * Starts profiling on CoreRequest, stops on CoreResponse
 */
class Profiler
{
    protected $profiler;
    protected $statsd;
    protected $sampling;

    public function __construct($profiler, $statsd, $logger, $sampling)
    {
        $this->profiler = $profiler;
        $this->statsd = $statsd;
        $this->logger = $logger;
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
                    $sample = $this->sampling/100;
                    $route = $event->getRequest()->attributes->get('_route');
                    foreach ($timers as $key => $value) {
                        $this->statsd->timing($key, $value, $sample);
                        if ($route) {
                            $this->statsd->timing("per_route.$key.$route", $value, $sample);
                        }
                    }
                    foreach ($counters as $key => $value) {
                        $this->statsd->updateStats($key, $value, $sample);
                        if ($route) {
                            $this->statsd->updateStats("per_route.$key.$route", $value, $sample);
                        }
                    }
                }
                if ($this->logger) {
                    $this->logger->log($event->getRequest(), $timers, $counters);
                }
            }
        }
    }
}
