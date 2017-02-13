<?php

namespace Socloz\MonitoringBundle\Notify\Message;

/**
 * Default notification message content
 */
class DefaultMessage extends AbstractMessage
{
    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return sprintf('Error message from %s - %s', $this->request->getHost(), $this->exception->getMessage());
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        $serverParams = $this->request->server->all();
        if (isset($serverParams['PHP_AUTH_PW'])) {
            $serverParams['PHP_AUTH_PW'] = '*****';
        }

        return $this->templating->render(
            "SoclozMonitoringBundle:Notify:exception.html.twig", array(
                'request' => $this->request,
                'exception' => $this->exception,
                'exception_class' => \get_class($this->exception),
                'request_headers' => $this->request->server->getHeaders(),
                'request_attributes' => $this->mailerTransformer->transform($this->request->attributes->all()),
                'server_params' => $this->mailerTransformer->transform($serverParams),
            )
        );
    }
}
