<?php

namespace Socloz\MonitoringBundle\Tests\Profiler;

use Socloz\MonitoringBundle\Tests\WebTestCase;

class HeaderTest extends WebTestCase
{
    public function testHeader()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/socloz_monitoring/request_id', array(), array(), array("HTTP_X_REQUESTID" => "foobar"));
        $this->assertEquals("foobar", $crawler->text());
    }
}
