<?php

namespace Socloz\MonitoringBundle\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Socloz\MonitoringBundle\Notify\Mailer;
use Socloz\MonitoringBundle\Notify\StatsD;

/**
 * Error notification listener
 *
 * @author Szymon Szewczyk <s.szewczyk@roxway.pl>
 * @author jfbus <jf@closetome.fr>
 */
class Exceptions
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var StatsD
     */
    protected $statsd;

    /**
     * @var array
     */
    protected $ignore;

    /**
     * @param Mailer $mailer
     * @param StatsD $statsd
     * @param        $ignore
     */
    public function __construct($mailer, $statsd, $ignore)
    {
        $this->mailer = $mailer;
        $this->statsd = $statsd;
        $this->ignore = is_array($ignore) ? $ignore : array($ignore);
    }

    /**
     * Exception error handler
     *
     * @param  GetResponseForExceptionEvent $event
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $class = get_class($event->getException());
        if (!in_array($class, $this->ignore)) {
            if ($this->mailer) {
                $this->mailer->sendException($event->getRequest(), $event->getException());
            }
            if ($this->statsd) {
                $this->statsd->increment("exception");
            }
        }
    }
}
