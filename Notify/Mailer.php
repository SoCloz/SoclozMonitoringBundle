<?php

namespace Socloz\MonitoringBundle\Notify;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;

/**
 * Error notification mailer
 *
 * @author Szymon Szewczyk <s.szewczyk@roxway.pl>
 */
class Mailer
{
    /**
     * SwiftMailer
     *
     * @var Object
     */
    protected $mailer;

    protected $templating;
    protected $from;
    protected $to;

    /**
     * @param \Swift_Mailer $mailer
     * @param               $templating
     * @param string        $from
     * @param string        $to
     * @param boolean       $enabled
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $templating, $from, $to, $enabled)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->from = $from;
        $this->to = $to;
        $this->enabled = $enabled;
    }

    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Send error notify mail
     *
     * @param Request    $request
     * @param \Exception $exception
     */
    public function sendException(Request $request, \Exception $exception)
    {
        if (!$this->enabled) {
            return;
        }

        $message = \Swift_Message::newInstance()
                ->setSubject('Error message from '.$request->getHost().' - '.$exception->getMessage())
                ->setFrom($this->from)
                ->setTo($this->to)
                ->setContentType('text/html')
                ->setBody(
                        $this->templating->render(
                                "SoclozMonitoringBundle:Notify:exception.html.twig", array(
                                    'request' => $request,
                                    'exception' => $exception,
                                    'exception_class' => \get_class($exception),
                                    'request_headers' => $request->server->getHeaders(),
                                    'request_attributes' => $request->attributes->all(),
                                    'server_params' => $request->server->all(),
                                )
                        )
                );

        try {
            $this->getMailer()->send($message);
        } catch (\Exception $e) {
            $this->getContainer()->get('logger')->err('Sending mail error - '.$e->getMessage());
        }
    }
}
