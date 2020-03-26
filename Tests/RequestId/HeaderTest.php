<?php

namespace Socloz\MonitoringBundle\Tests\RequestId;

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
