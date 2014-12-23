<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\Listener;

use Socloz\MonitoringBundle\Notify\Logger;
use Socloz\MonitoringBundle\Notify\StatsD\StatsDInterface;
use Socloz\MonitoringBundle\Profiler\Xhprof;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * The profiler
 * Starts profiling on CoreRequest, stops on CoreResponse
 */
class Profiler
{
    /**
     * @var Xhprof
     */
    protected $profiler;

    /**
     * @var StatsDInterface
     */
    protected $statsd;

    /**
     * @var int
     */
    protected $sampling;

    /**
     * @var int
     */
    protected $start;

    /**
     * @var boolean
     */
    protected $profiling;

    /**
     * @param Xhprof          $profiler
     * @param StatsDInterface $statsd
     * @param Logger          $logger
     * @param int             $sampling
     */
    public function __construct(Xhprof $profiler, StatsDInterface $statsd, Logger $logger = null, $sampling = 100)
    {
        $this->profiler = $profiler;
        $this->statsd = $statsd;
        $this->logger = $logger;
        $this->sampling = $sampling;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onCoreRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($this->sampling != 100 && mt_rand(1, 100) > $this->sampling) {
                return;
            }
            $this->profiler->startProfiling();
            $this->start = microtime(true);
            $this->profiling = true;
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onCoreResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->profiler->stopProfiling();
            if ($this->profiling) {
                $timers = $this->profiler->getTimers();
                $requestTime = microtime(true) - $this->start;
                $timers['request'] = (int)($requestTime * 1000);
                $counters = $this->profiler->getCounters();
                $counters['request'] = 1;
                if ($this->statsd) {
                    $sample = $this->sampling / 100;
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
