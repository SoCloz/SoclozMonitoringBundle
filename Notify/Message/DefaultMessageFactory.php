<?php

namespace Socloz\MonitoringBundle\Notify\Message;

use Socloz\MonitoringBundle\Transformer\MailerTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

/**
 * Create default notification message
 */
class DefaultMessageFactory implements MessageFactoryInterface
{
    /** @var EngineInterface */
    private $templating;

    /** @var MailerTransformer */
    private $mailerTransformer;

    /**
     * Constructor
     *
     * @param EngineInterface   $templating
     * @param MailerTransformer $mailerTransformer
     */
    public function __construct(EngineInterface $templating, MailerTransformer $mailerTransformer)
    {
        $this->templating = $templating;
        $this->mailerTransformer = $mailerTransformer;
    }

    /**
     * Create message instance
     *
     * @param Request    $request
     * @param \Exception $exception
     * @return MessageInterface
     */
    public function create(Request $request, \Exception $exception)
    {
        return new DefaultMessage($this->templating, $this->mailerTransformer, $request, $exception);
    }
}
