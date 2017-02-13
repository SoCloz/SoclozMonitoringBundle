<?php

namespace Socloz\MonitoringBundle\Notify\Message;

use Socloz\MonitoringBundle\Transformer\MailerTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

abstract class AbstractMessage implements MessageInterface
{
    /** @var EngineInterface */
    protected $templating;

    /** @var MailerTransformer */
    protected $mailerTransformer;

    /** @var Request */
    protected $request;

    /** @var \Exception */
    protected $exception;

    /**
     * Constructor
     *
     * @param EngineInterface   $templating
     * @param MailerTransformer $mailerTransformer
     * @param Request           $request
     * @param \Exception        $exception
     */
    public function __construct(EngineInterface $templating, MailerTransformer $mailerTransformer, Request $request, \Exception $exception)
    {
        $this->templating = $templating;
        $this->mailerTransformer = $mailerTransformer;
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'text/html';
    }
}
