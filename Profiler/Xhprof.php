<?php

/*
 * Copyright CloseToMe SAS 2013
 * Created by Jean-François Bustarret
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Socloz\MonitoringBundle\Profiler;

/**
 * Xhprof profiler
 */
class Xhprof extends AbstractProfiler
{
    /**
     * {@inheritdoc}
     */
    public function enableProfoling()
    {
        if (function_exists('xhprof_enable') && count($this->probes) > 0) {
            $this->profiling = true;
            xhprof_enable($this->memory ? XHPROF_FLAGS_MEMORY : null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disableProfiling()
    {
        return xhprof_disable();
    }
}
