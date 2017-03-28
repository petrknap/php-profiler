<?php

namespace PetrKnap\Php\Profiler;

interface ProfilerInterface
{
    /**
     * Start profiling
     *
     * @param string $labelOrFormat
     * @param mixed $args [optional]
     * @param mixed $_ [optional]
     * @return bool true on success or false on failure
     */
    public static function start($labelOrFormat = null, $args = null, $_ = null);

    /**
     * Finish profiling and get result
     *
     * @param string $labelOrFormat
     * @param mixed $args [optional]
     * @param mixed $_ [optional]
     * @return bool|Profile profile on success or false on failure
     */
    public static function finish($labelOrFormat = null, $args = null, $_ = null);
}
