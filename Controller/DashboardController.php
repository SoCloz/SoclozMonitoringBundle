<?php

namespace Socloz\MonitoringBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test controller 
 */
class TestController extends Controller
{
    /**
     * Generates an exception
     *
     * @Route("/socloz_monitoring/exception/{class}")
     * @Template()
     *
     * @param $class
     */
    public function generateExceptionAction($class)
    {
        $className = str_replace('__', '\\', $class);
        if (!strncmp($className, '\\', 1)) {
            $className = '\\'.$className;
        }
        throw new $className("Generated exception");
    }

    /**
     * Sleeps for a period of time
     *
     * @Route("/socloz_monitoring/timing/{time}")
     * @Template()
     *
     * @param $time
     *
     * @return Response
     */
    public function timingAction($time)
    {
        sleep($time);
        return new Response("waking up");
    }
}
