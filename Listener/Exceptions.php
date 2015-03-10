<?php

namespace Socloz\MonitoringBundle\Listener;

use Socloz\MonitoringBundle\Notify\Mailer;
use Socloz\MonitoringBundle\Notify\StatsD\StatsDInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Error notification listener
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
     * @var StatsDInterface
     */
    protected $statsd;

    /**
     * @var array
     */
    protected $ignore;

    /**
     * @param Mailer          $mailer
     * @param StatsDInterface $statsd
     * @param string|array    $ignore
     */
    public function __construct($mailer, StatsDInterface $statsd = null, $ignore = array())
    {
        $this->mailer = $mailer;
        $this->statsd = $statsd;
        $this->ignore = is_array($ignore) ? $ignore : array($ignore);
    }

    /**
     * Exception error handler
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->isIgnored($event->getException())) {
            return;
        }

        if ($this->mailer) {
            $this->mailer->sendException($event->getRequest(), $event->getException());
        }
        if ($this->statsd) {
            $this->statsd->increment("exception");
        }
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    private function isIgnored($object)
    {
        foreach ($this->ignore as $ignore) {
            if ($object instanceof $ignore) {
                return true;
            }
        }
        return false;
    }
}
