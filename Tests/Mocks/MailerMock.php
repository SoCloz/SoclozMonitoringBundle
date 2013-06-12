<?php

namespace Socloz\MonitoringBundle\Tests\Mocks;

/**
 * Mailer Mock
 *
 * @author jfb
 */
class MailerMock {

    protected $exceptions = array();
    
    public function __construct($mailer, $templating, $from, $to, $enabled) {
    }

    public function sendException($request, $exception) {
        $this->exceptions[] = $exception;
    }
    
    public function getExceptions() {
        return $this->exceptions;
    }
}
