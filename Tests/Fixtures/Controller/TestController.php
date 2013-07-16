<?php

namespace Socloz\MonitoringBundle\Tests\Fixtures\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test controller 
 */
class TestController extends Controller
{
    /**
     * Generates an exception
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
     */
    public function timingAction($time)
    {
        sleep($time);
        return new Response("waking up");
    }

    /**
     * Calls a specific, profiled function
     */
    public function pregAction($count)
    {
        $res = true;
        for ($i=0; $i<$count; $i++) {
            $res &= $this->doSomething();
        }
        return new Response($res ? "foo and bar match ?!?" : "foo and bar do not match");
    }

    /**
     * Echoes the request ID
     */
    public function requestIdAction()
    {
        $requestId = $this->get("socloz_monitoring.request_id");
        return new Response($requestId->getRequestId());
    }


    /**
     * Profiled function
     *
     * @return int
     */
    protected function doSomething()
    {
        return preg_match("/foo/", "bar");
    }
}
