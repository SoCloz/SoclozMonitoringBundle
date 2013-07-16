<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\RequestId;

/**
 * Generates RequestIds
 */
class Generator
{

    public function getRequestId()
    {
        return md5(uniqid(rand()));
    }
}