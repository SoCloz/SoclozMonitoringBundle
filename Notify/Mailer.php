<?php

namespace Socloz\MonitoringBundle\Notify;

use Psr\Log\LoggerInterface;
use Socloz\MonitoringBundle\Notify\Message\MessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Error notification mailer
 * @author Szymon Szewczyk <s.szewczyk@roxway.pl>
 */
class Mailer
{
    /**
     *
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var MessageFactoryInterface
     */
    protected $messageFactory;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @param \Swift_Mailer           $mailer
     * @param MessageFactoryInterface $messageFactory
     * @param LoggerInterface         $logger
     * @param string                  $from
     * @param string                  $to
     * @param boolean                 $enabled
     */
    public function __construct(\Swift_Mailer $mailer, MessageFactoryInterface $messageFactory, LoggerInterface $logger, $from, $to, $enabled)
    {
        $this->mailer = $mailer;
        $this->messageFactory = $messageFactory;
        $this->from = $from;
        $this->to = $to;
        $this->enabled = $enabled;
        $this->logger = $logger;
    }

    /**
     * @return \Swift_Mailer
     */
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

        $messageContent = $this->messageFactory->create($request, $exception);
        $message = \Swift_Message::newInstance()
            ->setSubject($messageContent->getSubject())
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setContentType($messageContent->getContentType())
            ->setBody($messageContent->getContent());

        try {
            $this->getMailer()->send($message);
        } catch (\Exception $e) {
            $this->logger->error('Sending mail error - '.$e->getMessage());
        }
    }
}
