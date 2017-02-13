<?php

namespace Socloz\MonitoringBundle\Profiler;

/**
 * Tideways profiler
 */
class Tideways extends AbstractProfiler
{
    /**
     * {@inheritdoc}
     */
    public function enableProfoling()
    {
        if (function_exists('tideways_enable') && count($this->probes) > 0) {
            $this->profiling = true;
            tideways_enable($this->memory ? TIDEWAYS_FLAGS_MEMORY : null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disableProfiling()
    {
        return tideways_disable();
    }
}
