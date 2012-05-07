<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Socloz\MonitoringBundle\Tests\Mocks;

/**
 * Description of StatsDMock
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
