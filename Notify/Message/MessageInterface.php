<?php

namespace Socloz\MonitoringBundle\Notify\Message;

interface MessageInterface
{
    /**
     * Get message subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * Get message content-type
     *
     * @return string
     */
    public function getContentType();

    /**
     * Get message content
     *
     * @return string
     */
    public function getContent();
}
